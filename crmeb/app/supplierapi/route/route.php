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
use app\http\middleware\AllowOriginMiddleware;
use app\supplierapi\middleware\AuthTokenMiddleware;
use think\facade\Config;
use think\facade\Route;
use think\Response;

Route::group(function () {

    // 登录（无需token）
    Route::group(function () {
        Route::post('login', 'Login/login')->name('supplierLogin')->option(['real_name' => '供应商账号登录']);
    })->option(['mark' => 'login', 'mark_name' => '登录']);

    // 以下需登录
    Route::group(function () {
        // 当前供应商信息 / 退出登录
        Route::get('info', 'Common/info')->option(['real_name' => '供应商信息']);
        Route::post('logout', 'Common/logout')->option(['real_name' => '退出登录']);

        // 我的研学项目
        Route::get('study_project', 'StudyProject/lst')->option(['real_name' => '我的研学项目列表']);
        Route::get('study_project/:id', 'StudyProject/detail')->option(['real_name' => '我的研学项目详情']);
    })->middleware(AuthTokenMiddleware::class)->option(['mark' => 'study_project', 'mark_name' => '研学项目']);

})->middleware(AllowOriginMiddleware::class);

Route::miss(function () {
    if (app()->request->isOptions()) {
        $header = Config::get('cookie.header');
        $header['Access-Control-Allow-Origin'] = app()->request->header('origin');
        return Response::create('ok')->code(200)->header($header);
    } else {
        return Response::create()->code(404);
    }
});
