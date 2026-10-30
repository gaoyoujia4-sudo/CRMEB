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
use think\facade\App;
use think\facade\Config;

/**
 * 供应商端 · 通用接口（需登录）
 * Class Common
 * @package app\supplierapi\controller
 */
class Common extends AuthController
{
    /**
     * 构造方法
     * Common constructor.
     * @param App $app
     * @param ServiceProviderAccountServices $services
     */
    public function __construct(App $app, ServiceProviderAccountServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    /**
     * 当前供应商信息
     * @return mixed
     */
    public function info()
    {
        return app('json')->success($this->supplierInfo);
    }

    /**
     * 退出登录
     * @param Request $request
     * @return mixed
     */
    public function logout(Request $request)
    {
        $token = trim(ltrim($request->header(Config::get('cookie.token_name', 'Authori-zation')), 'Bearer'));
        $this->services->logout($token);
        return app('json')->success('退出成功');
    }
}
