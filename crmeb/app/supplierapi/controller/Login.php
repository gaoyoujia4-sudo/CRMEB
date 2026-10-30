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
namespace app\supplierapi\controller;

use app\Request;
use app\services\study\ServiceProviderAccountServices;
use app\supplierapi\validate\LoginValidate;
use think\facade\App;

/**
 * 供应商端登录
 * Class Login
 * @package app\supplierapi\controller
 */
class Login extends AuthController
{
    /**
     * 构造方法
     * Login constructor.
     * @param App $app
     * @param ServiceProviderAccountServices $services
     */
    public function __construct(App $app, ServiceProviderAccountServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    protected function initialize()
    {
        // 登录接口无需 token 校验
    }

    /**
     * 账号密码登录
     * @param Request $request
     * @return mixed
     */
    public function login(Request $request)
    {
        [$account, $password] = $request->postMore([
            ['account', ''],
            ['password', ''],
        ], true);
        validate(LoginValidate::class)->check(['account' => $account, 'password' => $password]);
        $token = $this->services->authLogin($account, $password);
        return app('json')->success('登录成功', $token);
    }
}
