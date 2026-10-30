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

class StudyProjectValidate extends Validate
{
    /**
     * 定义验证规则
     * @var array
     */
    protected $rule = [
        'title' => 'require',
        'price_type' => 'require|in:1,2',
        'product_id' => 'integer',
    ];

    /**
     * 定义错误信息
     * @var array
     */
    protected $message = [
        'title.require' => '请填写项目名称',
        'price_type.require' => '请选择价格类型',
        'price_type.in' => '价格类型取值错误',
    ];

    protected $scene = [
        'save' => ['title', 'price_type'],
    ];
}
