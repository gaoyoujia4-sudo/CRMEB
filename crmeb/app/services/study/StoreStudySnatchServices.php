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

use app\dao\study\StoreStudySnatchDao;
use app\services\BaseServices;
use app\services\order\StoreOrderServices;
use app\services\product\product\StoreProductServices;
use app\services\product\sku\StoreProductAttrValueServices;
use crmeb\exceptions\AdminException;
use crmeb\exceptions\ApiException;

/**
 * 研学抢购场次
 * Class StoreStudySnatchServices
 * @package app\services\study
 * @method update($id, array $data, ?string $key = null) 修改数据
 * @method get(int $id, ?array $field = []) 获取数据
 */
class StoreStudySnatchServices extends BaseServices
{
    /**
     * 「研学」商品分类ID
     */
    const STUDY_CATE_ID = 41;

    /**
     * 构造方法
     * StoreStudySnatchServices constructor.
     * @param StoreStudySnatchDao $dao
     */
    public function __construct(StoreStudySnatchDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * 计算场次抢购状态与倒计时目标时间
     * snatch_status: 0 已关闭/已结束 1 进行中 2 未开始 3 已售罄
     * @param array $item
     */
    protected function computeSnatchStatus(array &$item): void
    {
        $now = time();
        $item['snatch_status'] = 0;
        $item['last_time'] = 0;
        if ($item['status'] != 1) {
            return;
        }
        if ($item['start_time'] > $now) {
            $item['snatch_status'] = 2;
            $item['last_time'] = (int)$item['start_time'];
        } elseif ($item['stop_time'] < $now) {
            $item['snatch_status'] = 0;
        } elseif ($item['quota'] <= 0) {
            $item['snatch_status'] = 3;
        } else {
            $item['snatch_status'] = 1;
            $item['last_time'] = (int)$item['stop_time'];
        }
        $item['percent'] = $item['quota_show'] > 0 ? (int)round(($item['quota_show'] - $item['quota']) * 100 / $item['quota_show']) : 0;
    }

    /**
     * 单条场次信息（购物车缓存嵌入用）
     * @param int $id
     * @return array
     */
    public function getSnatchInfo(int $id): array
    {
        if (!$id) return [];
        $info = $this->dao->get($id);
        if (!$info) return [];
        $info = $info->toArray();
        if ($info['is_del'] || $info['status'] != 1) return [];
        $this->computeSnatchStatus($info);
        return $info;
    }

    /**
     * 商品详情场次列表
     * @param int $productId
     * @return array
     */
    public function getProductSnatchList(int $productId): array
    {
        if (!$productId) return [];
        $list = $this->dao->search(['product_id' => $productId, 'status' => 1, 'is_del' => 0])
            ->order('sort desc,id desc')->select()->toArray();
        foreach ($list as &$item) {
            $this->computeSnatchStatus($item);
        }
        unset($item);
        return $list;
    }

    /**
     * 研学抢购聚合列表（只返回有未开始/进行中场次的研学商品）
     * @param array $where
     * @return array
     */
    public function getSnatchIndex(array $where)
    {
        [$page, $limit] = $this->getPageValue();
        $list = $this->dao->search(['status' => 1, 'is_del' => 0])
            ->where('stop_time', '>', time())
            ->with(['product'])
            ->when($page && $limit, function ($query) use ($page, $limit) {
                $query->page($page, $limit);
            })
            ->order('sort desc,id desc')->select()->toArray();
        $result = [];
        foreach ($list as $item) {
            $this->computeSnatchStatus($item);
            if (in_array($item['snatch_status'], [1, 2])) {
                // 原价兜底：场次未填划线原价时，取研学商品的原价，保证前端能看到产品价格
                if (empty($item['ot_price']) && !empty($item['product_ot_price'])) {
                    $item['ot_price'] = $item['product_ot_price'];
                }
                $result[] = $item;
            }
        }
        return $result;
    }

    /**
     * 后台场次分页列表
     * @param array $where
     * @return array
     */
    public function getSnatchList(array $where)
    {
        [$page, $limit] = $this->getPageValue();
        $list = $this->dao->search($where)->with(['product'])
            ->when($page && $limit, function ($query) use ($page, $limit) {
                $query->page($page, $limit);
            })
            ->order('sort desc,id desc')->select()->toArray();
        foreach ($list as &$item) {
            $this->computeSnatchStatus($item);
        }
        unset($item);
        $count = $this->dao->count($where);
        return compact('list', 'count');
    }

    /**
     * 保存或修改场次
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function saveSnatch(int $id, array $data)
    {
        // 时间字符串转 int 时间戳
        foreach (['start_time', 'stop_time'] as $key) {
            if (isset($data[$key]) && !is_numeric($data[$key])) {
                $data[$key] = strtotime($data[$key]);
            }
        }
        if (isset($data['num'], $data['once_num']) && $data['num'] < $data['once_num']) {
            throw new AdminException('每人限购数量不能小于单次限购数量');
        }
        // 必须是研学商品（商品归入「研学」分类或其子分类）
        $productId = (int)($data['product_id'] ?? 0);
        if (!$productId) throw new AdminException('请选择研学商品');
        $cateIds = \think\facade\Db::name('store_category')->where('id', self::STUDY_CATE_ID)->whereOr('pid', self::STUDY_CATE_ID)->column('id');
        $isStudy = \think\facade\Db::name('store_product_cate')->where('product_id', $productId)->whereIn('cate_id', $cateIds)->value('product_id');
        if (!$isStudy) throw new AdminException('所选商品不是研学商品');
        // 编辑已保存场次禁止修改总名额，防止 quota+sales!=quota_show
        if ($id && isset($data['quota_show'])) {
            $old = $this->dao->get($id);
            if ($old && $old['quota_show'] != $data['quota_show']) {
                throw new AdminException('已保存的场次不允许修改总名额');
            }
        }
        return $this->transaction(function () use ($id, $data) {
            if ($id) {
                $data['update_time'] = time();
                if ($this->dao->update($id, $data)) {
                    return true;
                }
                throw new AdminException('修改失败');
            } else {
                $data['quota'] = (int)($data['quota_show'] ?? 0);
                $data['sales'] = 0;
                $data['add_time'] = time();
                $data['update_time'] = time();
                if ($this->dao->save($data)) {
                    return true;
                }
                throw new AdminException('保存失败');
            }
        });
    }

    /**
     * 软删场次
     * @param int $id
     * @return bool
     */
    public function deleteSnatch(int $id): bool
    {
        return (bool)$this->dao->update($id, ['is_del' => 1]);
    }

    /**
     * 抢购库存/限购校验（加购时），返回 [attrInfo, unique, productInfo]
     * @param int $uid
     * @param int $snatchId
     * @param int $cartNum
     * @param string $unique
     * @return array
     */
    public function checkSnatchStock(int $uid, int $snatchId, int $cartNum = 1, string $unique = ''): array
    {
        $snatch = $this->dao->get($snatchId);
        if (!$snatch || $snatch['is_del'] || $snatch['status'] != 1) {
            throw new ApiException('抢购场次不存在或已关闭');
        }
        $snatchInfo = $snatch->toArray();
        $now = time();
        if ($snatchInfo['start_time'] > $now) throw new ApiException('抢购尚未开始');
        if ($snatchInfo['stop_time'] < $now) throw new ApiException('抢购已结束');
        if ($snatchInfo['quota'] <= 0) throw new ApiException('该场次名额已抢完');
        if ($snatchInfo['once_num'] > 0 && $snatchInfo['once_num'] < $cartNum) {
            throw new ApiException('每个订单限购{:num}个名额', ['num' => $snatchInfo['once_num']]);
        }
        /** @var StoreOrderServices $orderServices */
        $orderServices = app()->make(StoreOrderServices::class);
        $userBuyCount = $orderServices->getBuyCount($uid, 'study_snatch_id', $snatchId);
        if ($snatchInfo['num'] < ($userBuyCount + $cartNum)) {
            throw new ApiException('每人总共限购{:num}个名额', ['num' => $snatchInfo['num']]);
        }
        if ($snatchInfo['quota'] < $cartNum) throw new ApiException('该场次剩余名额不足');
        /** @var StoreProductServices $productServices */
        $productServices = app()->make(StoreProductServices::class);
        $productInfo = $productServices->isValidProduct((int)$snatchInfo['product_id']);
        if (!$productInfo) throw new ApiException('该商品已下架或删除');
        if ($productInfo['stock'] < $cartNum) throw new ApiException('该商品库存不足');
        /** @var StoreProductAttrValueServices $attrValueServices */
        $attrValueServices = app()->make(StoreProductAttrValueServices::class);
        if ($unique == '') {
            $unique = $attrValueServices->value(['product_id' => $snatchInfo['product_id'], 'type' => 0], 'unique');
        }
        $attrInfo = $attrValueServices->getOne(['unique' => $unique, 'type' => 0]);
        if (!$unique || !$attrInfo || $attrInfo['product_id'] != $snatchInfo['product_id']) {
            throw new ApiException('请选择有效的商品属性');
        }
        // 覆盖为抢购价
        $attrInfo->price = $snatchInfo['price'];
        return [$attrInfo, $unique, $productInfo];
    }

    /**
     * 扣减名额并同步扣商品总库存（下单）
     * @param int $num
     * @param int $snatchId
     * @return bool
     */
    public function decSnatchStock(int $num, int $snatchId): bool
    {
        $res = $this->dao->decSnatchQuotaIncSales($snatchId, $num);
        if (!$res) return false;
        $product_id = (int)$this->dao->value(['id' => $snatchId], 'product_id');
        return app()->make(StoreProductServices::class)->decProductStock($num, $product_id);
    }

    /**
     * 回补名额并同步回补商品总库存（取消/退款）
     * @param int $num
     * @param int $snatchId
     * @return bool
     */
    public function incSnatchStock(int $num, int $snatchId): bool
    {
        $this->dao->incSnatchQuotaDecSales($snatchId, $num);
        $product_id = (int)$this->dao->value(['id' => $snatchId], 'product_id');
        return app()->make(StoreProductServices::class)->incProductStock($num, $product_id);
    }
}
