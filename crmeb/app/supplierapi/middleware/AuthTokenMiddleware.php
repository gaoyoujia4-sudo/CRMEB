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
namespace app\supplierapi\middleware;

use app\Request;
use app\services\study\ServiceProviderAccountServices;
use crmeb\interfaces\MiddlewareInterface;
use think\facade\Config;

/**
 * 供应商端token校验
 * Class AuthTokenMiddleware
 * @package app\supplierapi\middleware
 */
class AuthTokenMiddleware implements MiddlewareInterface
{
    /**
     * @param Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, \Closure $next)
    {
        $token = trim(ltrim($request->header(Config::get('cookie.token_name', 'Authori-zation')), 'Bearer'));
        /** @var ServiceProviderAccountServices $services */
        $services = app()->make(ServiceProviderAccountServices::class);
        $supplierInfo = $services->parseToken($token);

        $request->macro('supplierId', function () use (&$supplierInfo) {
            return (int)$supplierInfo['id'];
        });

        $request->macro('supplierInfo', function () use (&$supplierInfo) {
            return $supplierInfo;
        });

        return $next($request);
    }
}
