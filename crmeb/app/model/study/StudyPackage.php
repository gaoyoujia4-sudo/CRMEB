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

namespace app\model\study;

use app\model\product\product\StoreProduct;
use crmeb\basic\BaseModel;
use crmeb\traits\ModelTrait;
use think\Model;

/**
 * 研学套餐
 * Class StudyPackage
 * @package app\model\study
 */
class StudyPackage extends BaseModel
{
    use ModelTrait;

    /**
     * 数据表主键
     * @var string
     */
    protected $pk = 'id';

    /**
     * 模型名称
     * @var string
     */
    protected $name = 'study_package';

    /**
     * 时间字段为int时间戳，关闭自动时间戳转换
     * @var bool
     */
    protected $autoWriteTimestamp = false;

    /**
     * 一对一关联研学商品（列表展示商品图/名用）
     * @return \think\model\relation\HasOne
     */
    public function product()
    {
        return $this->hasOne(StoreProduct::class, 'id', 'product_id')
            ->field(['id', 'store_name', 'image', 'price', 'ot_price'])
            ->bind([
                'store_name' => 'store_name',
                'image' => 'image',
                'product_price' => 'price',
                'product_ot_price' => 'ot_price',
            ]);
    }

    /**
     * 商品搜索器
     * @param Model $query
     * @param $value
     */
    public function searchProductIdAttr($query, $value)
    {
        if ((int)$value > 0) {
            $query->where('product_id', $value);
        }
    }

    /**
     * 团型搜索器
     * @param Model $query
     * @param $value
     */
    public function searchGroupTypeAttr($query, $value)
    {
        if ((int)$value > 0) {
            $query->where('group_type', $value);
        }
    }

    /**
     * 场次类型搜索器
     * @param Model $query
     * @param $value
     */
    public function searchSessionTypeAttr($query, $value)
    {
        if ((int)$value > 0) {
            $query->where('session_type', $value);
        }
    }

    /**
     * 状态搜索器
     * @param Model $query
     * @param $value
     */
    public function searchStatusAttr($query, $value)
    {
        if ($value !== '' && $value !== null) {
            $query->where('status', $value);
        }
    }

    /**
     * 是否删除搜索器
     * @param Model $query
     * @param $value
     */
    public function searchIsDelAttr($query, $value)
    {
        if ($value !== '' && $value !== null) {
            $query->where('is_del', $value);
        }
    }

    /**
     * 关键词搜索器（套餐名）
     * @param Model $query
     * @param $value
     */
    public function searchKeywordsAttr($query, $value)
    {
        if ($value !== '') {
            $query->whereLike('title', '%' . $value . '%');
        }
    }
}
