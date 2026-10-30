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

use app\dao\study\StudyPackageDao;
use app\services\BaseServices;
use app\services\product\product\StoreProductServices;
use app\services\product\sku\StoreProductAttrValueServices;
use crmeb\exceptions\AdminException;
use crmeb\exceptions\ApiException;

/**
 * 研学套餐
 * Class StudyPackageServices
 * @package app\services\study
 * @method update($id, array $data, ?string $key = null) 修改数据
 * @method get(int $id, ?array $field = []) 获取数据
 */
class StudyPackageServices extends BaseServices
{
    /**
     * 「研学」商品分类ID
     */
    const STUDY_CATE_ID = 41;

    /**
     * 构造方法
     * StudyPackageServices constructor.
     * @param StudyPackageDao $dao
     */
    public function __construct(StudyPackageDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * 后台套餐分页列表
     * @param array $where
     * @return array
     */
    public function getPackageList(array $where)
    {
        [$page, $limit] = $this->getPageValue();
        $list = $this->dao->search($where)->with(['product'])
            ->when($page && $limit, function ($query) use ($page, $limit) {
                $query->page($page, $limit);
            })
            ->order('sort desc,id desc')->select()->toArray();
        $count = $this->dao->count($where);
        return compact('list', 'count');
    }

    /**
     * 保存或修改套餐
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function savePackage(int $id, array $data)
    {
        // 必须是研学商品（商品归入「研学」分类或其子分类）
        $productId = (int)($data['product_id'] ?? 0);
        if (!$productId) throw new AdminException('请选择研学商品');
        $cateIds = \think\facade\Db::name('store_category')->where('id', self::STUDY_CATE_ID)->whereOr('pid', self::STUDY_CATE_ID)->column('id');
        $isStudy = \think\facade\Db::name('store_product_cate')->where('product_id', $productId)->whereIn('cate_id', $cateIds)->value('product_id');
        if (!$isStudy) throw new AdminException('所选商品不是研学商品');
        return $this->transaction(function () use ($id, $data) {
            if ($id) {
                $data['update_time'] = time();
                if ($this->dao->update($id, $data)) {
                    return true;
                }
                throw new AdminException('修改失败');
            } else {
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
     * 软删套餐
     * @param int $id
     * @return bool
     */
    public function deletePackage(int $id): bool
    {
        return (bool)$this->dao->update($id, ['is_del' => 1]);
    }

    /**
     * 商品详情页套餐列表（上架中，支持团型/场次筛选）
     * @param int $productId
     * @param int $groupType
     * @param int $sessionType
     * @return array
     */
    public function getProductPackageList(int $productId, int $groupType = 0, int $sessionType = 0): array
    {
        if (!$productId) return [];
        return $this->dao->search([
            'product_id' => $productId,
            'group_type' => $groupType,
            'session_type' => $sessionType,
            'status' => 1,
            'is_del' => 0,
        ])->order('sort desc,id desc')->select()->toArray();
    }

    /**
     * 有效套餐信息（下单校验用）
     * @param int $id
     * @return array
     */
    public function getValidPackageInfo(int $id): array
    {
        if (!$id) return [];
        $info = $this->dao->get($id);
        if (!$info) return [];
        $info = $info->toArray();
        if ($info['is_del'] || $info['status'] != 1) return [];
        return $info;
    }

    /**
     * 套餐库存校验（加购时），返回 [attrInfo, unique, productInfo]
     * @param int $uid
     * @param int $packageId
     * @param int $cartNum
     * @param string $unique
     * @return array
     */
    public function checkPackageStock(int $uid, int $packageId, int $cartNum = 1, string $unique = ''): array
    {
        $package = $this->dao->get($packageId);
        if (!$package || $package['is_del'] || $package['status'] != 1) {
            throw new ApiException('套餐不存在或已下架');
        }
        $packageInfo = $package->toArray();
        /** @var StoreProductServices $productServices */
        $productServices = app()->make(StoreProductServices::class);
        $productInfo = $productServices->isValidProduct((int)$packageInfo['product_id']);
        if (!$productInfo) throw new ApiException('该商品已下架或删除');
        if ($productInfo['stock'] < $cartNum) throw new ApiException('该商品库存不足');
        /** @var StoreProductAttrValueServices $attrValueServices */
        $attrValueServices = app()->make(StoreProductAttrValueServices::class);
        if ($unique == '') {
            $unique = $attrValueServices->value(['product_id' => $packageInfo['product_id'], 'type' => 0], 'unique');
        }
        $attrInfo = $attrValueServices->getOne(['unique' => $unique, 'type' => 0]);
        if (!$unique || !$attrInfo || $attrInfo['product_id'] != $packageInfo['product_id']) {
            throw new ApiException('请选择有效的商品属性');
        }
        if ($attrInfo['stock'] < $cartNum) throw new ApiException('该商品库存不足');
        // 覆盖为套餐价
        $attrInfo->price = $packageInfo['price'];
        return [$attrInfo, $unique, $productInfo];
    }
}
