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
namespace app\supplierapi\validate;

use think\Validate;

class LoginValidate extends Validate
{
    /**
     * 定义验证规则
     * @var array
     */
    protected $rule = [
        'account' => 'require',
        'password' => 'require',
    ];

    /**
     * 定义错误信息
     * @var array
     */
    protected $message = [
        'account.require' => '请输入账号和密码',
        'password.require' => '请输入账号和密码',
    ];
}
