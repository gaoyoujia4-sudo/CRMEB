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
namespace app\adminapi\validate\study;

use think\Validate;

class StudyPackageValidate extends Validate
{
    /**
     * 定义验证规则
     * @var array
     */
    protected $rule = [
        'product_id' => 'require',
        'group_type' => 'require|in:1,2',
        'session_type' => 'require|in:1,2,3',
        'title' => 'require',
        'price' => 'require',
    ];

    /**
     * 定义错误信息
     * @var array
     */
    protected $message = [
        'product_id.require' => '请选择研学商品',
        'group_type.require' => '请选择团型',
        'group_type.in' => '团型不正确',
        'session_type.require' => '请选择场次类型',
        'session_type.in' => '场次类型不正确',
        'title.require' => '请填写套餐名称',
        'price.require' => '请填写套餐价',
    ];

    protected $scene = [
        'save' => ['product_id', 'group_type', 'session_type', 'title', 'price'],
    ];
}
