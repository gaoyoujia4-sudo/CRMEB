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

class ServiceProviderAccountValidate extends Validate
{
    /**
     * 定义验证规则
     * @var array
     */
    protected $rule = [
        'account' => 'require',
        'provider_id' => 'require|number',
    ];

    /**
     * 定义错误信息
     * @var array
     */
    protected $message = [
        'account.require' => '请填写登录账号',
        'provider_id.require' => '请选择服务商',
        'provider_id.number' => '服务商参数错误',
    ];

    protected $scene = [
        'save' => ['account', 'provider_id'],
    ];
}
