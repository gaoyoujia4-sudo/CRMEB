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

use crmeb\basic\BaseModel;
use crmeb\traits\ModelTrait;
use think\Model;

/**
 * 研学项目
 * Class StudyProject
 * @package app\model\study
 */
class StudyProject extends BaseModel
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
    protected $name = 'study_project';

    /**
     * 时间字段为int时间戳，关闭自动时间戳转换
     * @var bool
     */
    protected $autoWriteTimestamp = false;

    /**
     * 关键词搜索器（项目名称）
     * @param Model $query
     * @param $value
     */
    public function searchKeywordsAttr($query, $value)
    {
        if ($value !== '') {
            $query->whereLike('title', '%' . $value . '%');
        }
    }

    /**
     * 价格类型搜索器
     * @param Model $query
     * @param $value
     */
    public function searchPriceTypeAttr($query, $value)
    {
        if ($value !== '' && $value !== null) {
            $query->where('price_type', $value);
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
     * 服务商id搜索器
     * @param Model $query
     * @param $value
     */
    public function searchServiceProviderIdAttr($query, $value)
    {
        if ($value !== '' && $value !== null) {
            $query->where('service_provider_id', $value);
        }
    }
}
