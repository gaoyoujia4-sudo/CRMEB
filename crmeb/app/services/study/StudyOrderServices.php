<?php
// +----------------------------------------------------------------------
// | CRMEB [ CRMEB赋能开发者，助力企业发展 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2026 https://www.crmeb.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed CRMEB并不是自由软件，未经许可不能去掉CRMEB相关版权
// +----------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +----------------------------------------------------------------------

namespace app\services\study;

use app\model\order\StoreOrder;
use app\model\order\StoreOrderCartInfo;
use app\model\study\StoreStudySnatch;
use app\model\study\StudyProject;
use app\services\BaseServices;

/**
 * 研学抢购人（研学订单）服务
 * Class StudyOrderServices
 * @package app\services\study
 */
class StudyOrderServices extends BaseServices
{
    /**
     * 判断订单是否为研学订单
     * 口径与 getStudyBuyerList 一致：
     * 1. 抢购场次订单（study_snatch_id > 0）
     * 2. 研学套餐订单（study_package_id > 0）
     * 3. 研学项目订单（订单内商品 product_id 关联了研学项目）
     * @param array|\think\Model $order
     * @return bool
     */
    public function isStudyOrder($order): bool
    {
        if (is_object($order)) {
            $order = $order->toArray();
        }
        if (!empty($order['study_snatch_id']) || !empty($order['study_package_id'])) {
            return true;
        }
        $oid = (int)($order['id'] ?? 0);
        if (!$oid) {
            return false;
        }
        $projectProductIds = StudyProject::where('product_id', '>', 0)->column('product_id');
        if (!$projectProductIds) {
            return false;
        }
        $cartProductIds = StoreOrderCartInfo::where('oid', $oid)->column('product_id');
        return (bool)array_intersect(array_map('intval', $projectProductIds), array_map('intval', $cartProductIds));
    }

    /**
     * 批量判断订单是否为研学订单（口径与 isStudyOrder 一致），返回研学订单id集合
     * @param array $orders 订单数组，元素需含 id、study_snatch_id、study_package_id
     * @return array
     */
    public function getStudyOrderIds(array $orders): array
    {
        $studyIds = [];
        $cartOids = [];
        foreach ($orders as $order) {
            $oid = (int)($order['id'] ?? 0);
            if (!$oid) {
                continue;
            }
            if (!empty($order['study_snatch_id']) || !empty($order['study_package_id'])) {
                $studyIds[$oid] = $oid;
            } else {
                $cartOids[] = $oid;
            }
        }
        if ($cartOids) {
            $projectProductIds = StudyProject::where('product_id', '>', 0)->column('product_id');
            if ($projectProductIds) {
                $studyOids = StoreOrderCartInfo::whereIn('oid', $cartOids)
                    ->whereIn('product_id', array_map('intval', $projectProductIds))
                    ->group('oid')
                    ->column('oid');
                foreach ($studyOids as $oid) {
                    $studyIds[(int)$oid] = (int)$oid;
                }
            }
        }
        return array_values($studyIds);
    }

    /**
     * 生成研学订单核销码（幂等：已有核销码则跳过）
     * @param int $oid
     * @return bool
     */
    public function ensureVerifyCode(int $oid): bool
    {
        if (!$oid) {
            return false;
        }
        $order = StoreOrder::find($oid);
        if (!$order || $order->verify_code) {
            return false;
        }
        $order->verify_code = app()->make(\app\services\order\StoreOrderCreateServices::class)->getStoreCode();
        return (bool)$order->save();
    }

    /**
     * 研学抢购人列表
     * 数据来源：
     * 1.研学抢购订单（study_snatch_id > 0 且已支付）
     * 2.研学套餐订单（study_package_id > 0 且已支付）
     * 3.研学项目订单（订单商品关联了研学项目 product_id 且已支付）
     * @param array $where
     * @return array
     */
    public function getStudyBuyerList(array $where)
    {
        [$page, $limit] = $this->getPageValue();

        //研学项目关联的商品id => 项目标题
        $projectMap = StudyProject::where('product_id', '>', 0)->column('title', 'product_id');
        $projectOids = [];
        if ($projectMap) {
            $projectOids = StoreOrderCartInfo::whereIn('product_id', array_keys($projectMap))
                ->group('oid')
                ->column('oid');
        }

        $query = StoreOrder::where('paid', 1)
            ->where('is_del', 0)
            ->where('is_system_del', 0)
            ->where(function ($q) use ($projectOids) {
                $q->where('study_snatch_id', '>', 0);
                $q->whereOr('study_package_id', '>', 0);
                if ($projectOids) {
                    $q->whereOr('id', 'in', $projectOids);
                }
            });

        //关键词：订单号 / 姓名 / 手机号 / 身份证号
        if (!empty($where['keywords'])) {
            $kw = trim($where['keywords']);
            $query->where(function ($q) use ($kw) {
                $q->where('order_id', 'like', '%' . $kw . '%')
                    ->whereOr('real_name', 'like', '%' . $kw . '%')
                    ->whereOr('user_phone', 'like', '%' . $kw . '%')
                    ->whereOr('user_id_card', 'like', '%' . $kw . '%');
            });
        }

        //订单状态筛选
        if (isset($where['status']) && $where['status'] !== '') {
            $query->where('status', $where['status']);
        }

        $count = $query->count();

        $field = [
            'id', 'order_id', 'real_name', 'user_phone', 'user_id_card',
            'study_snatch_id', 'study_package_id', 'study_package_title', 'study_travel_date',
            'lecturer_name', 'pay_price', 'add_time', 'status', 'verify_code',
        ];
        $list = $query->field($field)->order('id', 'desc')->page($page, $limit)->select()->toArray();

        if ($list) {
            //场次标题
            $snatchIds = array_unique(array_filter(array_map('intval', array_column($list, 'study_snatch_id'))));
            $snatchTitleMap = $snatchIds ? StoreStudySnatch::whereIn('id', $snatchIds)->column('title', 'id') : [];

            //商品名称、套餐类型（来自订单购物车快照）
            $oids = array_column($list, 'id');
            $cartRows = StoreOrderCartInfo::whereIn('oid', $oids)
                ->field(['oid', 'product_id', 'cart_info', 'cart_num'])
                ->select()
                ->toArray();

            $cartMap = [];
            $orderProductIds = [];
            foreach ($cartRows as $row) {
                $orderProductIds[$row['oid']][] = (int)$row['product_id'];
                $info = is_array($row['cart_info']) ? $row['cart_info'] : json_decode((string)$row['cart_info'], true);
                $productInfo = is_array($info) ? ($info['productInfo'] ?? []) : [];
                $storeName = $productInfo['store_name'] ?? '';
                $suk = $productInfo['attrInfo']['suk'] ?? '';
                if (!isset($cartMap[$row['oid']])) {
                    $cartMap[$row['oid']] = [
                        'store_name' => $storeName,
                        'suk' => $suk,
                        'cart_num' => (int)$row['cart_num'],
                    ];
                } else {
                    //一单多商品时仅做标记，名称合并展示
                    if ($storeName && $cartMap[$row['oid']]['store_name'] && $storeName != $cartMap[$row['oid']]['store_name']) {
                        $cartMap[$row['oid']]['store_name'] .= ' 等';
                    }
                    if ($suk && $cartMap[$row['oid']]['suk'] && $suk != $cartMap[$row['oid']]['suk']) {
                        $cartMap[$row['oid']]['suk'] .= ' 等';
                    }
                    $cartMap[$row['oid']]['cart_num'] += (int)$row['cart_num'];
                }
            }

            foreach ($list as &$item) {
                $item['snatch_title'] = $snatchTitleMap[(int)$item['study_snatch_id']] ?? '';
                //研学项目订单：场次列展示项目名称
                if (!$item['snatch_title']) {
                    foreach ($orderProductIds[$item['id']] ?? [] as $pid) {
                        if (isset($projectMap[$pid])) {
                            $item['snatch_title'] = $projectMap[$pid];
                            break;
                        }
                    }
                }
                $item['store_name'] = $cartMap[$item['id']]['store_name'] ?? '';
                $item['suk'] = $cartMap[$item['id']]['suk'] ?? '';
                $item['cart_num'] = $cartMap[$item['id']]['cart_num'] ?? 0;
                $item['pay_price'] = (float)$item['pay_price'];
                $item['add_time_text'] = $item['add_time'] ? date('Y-m-d H:i:s', (int)$item['add_time']) : '';
            }
            unset($item);
        }

        return ['list' => $list, 'count' => $count];
    }
}
