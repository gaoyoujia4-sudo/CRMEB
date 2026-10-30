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
use think\facade\Route;

/**
 * 研学模块 相关路由
 */
Route::group('study', function () {

    /** 讲师 */
    Route::group(function () {
        //讲师列表
        Route::get('lecturer', 'v1.study.Lecturer/index')->option(['real_name' => '研学讲师列表']);
        //全部启用的讲师（下拉选择）
        Route::get('lecturer/all', 'v1.study.Lecturer/all')->option(['real_name' => '研学讲师下拉列表']);
        //讲师详情
        Route::get('lecturer/:id', 'v1.study.Lecturer/read')->option(['real_name' => '研学讲师详情']);
        //保存修改讲师
        Route::post('lecturer/:id', 'v1.study.Lecturer/save')->option(['real_name' => '保存研学讲师']);
        //删除讲师
        Route::delete('lecturer/del/:id', 'v1.study.Lecturer/delete')->option(['real_name' => '删除研学讲师']);
    })->option(['parent' => 'study', 'cate_name' => '讲师']);

    /** 服务商 */
    Route::group(function () {
        //服务商列表
        Route::get('provider', 'v1.study.ServiceProvider/index')->option(['real_name' => '研学服务商列表']);
        //全部启用的服务商（下拉选择）
        Route::get('provider/all', 'v1.study.ServiceProvider/all')->option(['real_name' => '研学服务商下拉列表']);
        //服务商详情
        Route::get('provider/:id', 'v1.study.ServiceProvider/read')->option(['real_name' => '研学服务商详情']);
        //保存修改服务商
        Route::post('provider/:id', 'v1.study.ServiceProvider/save')->option(['real_name' => '保存研学服务商']);
        //删除服务商
        Route::delete('provider/del/:id', 'v1.study.ServiceProvider/delete')->option(['real_name' => '删除研学服务商']);
    })->option(['parent' => 'study', 'cate_name' => '服务商']);

    /** 供应商账号 */
    Route::group(function () {
        //账号列表
        Route::get('provider_account', 'v1.study.ServiceProviderAccount/index')->option(['real_name' => '研学供应商账号列表']);
        //账号详情
        Route::get('provider_account/:id', 'v1.study.ServiceProviderAccount/read')->option(['real_name' => '研学供应商账号详情']);
        //保存修改账号
        Route::post('provider_account/:id', 'v1.study.ServiceProviderAccount/save')->option(['real_name' => '保存研学供应商账号']);
        //删除账号
        Route::delete('provider_account/del/:id', 'v1.study.ServiceProviderAccount/delete')->option(['real_name' => '删除研学供应商账号']);
    })->option(['parent' => 'study', 'cate_name' => '供应商账号']);

    /** 研学项目 */
    Route::group(function () {
        //项目列表
        Route::get('project', 'v1.study.StudyProject/index')->option(['real_name' => '研学项目列表']);
        //项目详情
        Route::get('project/:id', 'v1.study.StudyProject/read')->option(['real_name' => '研学项目详情']);
        //保存修改项目
        Route::post('project/:id', 'v1.study.StudyProject/save')->option(['real_name' => '保存研学项目']);
        //删除项目
        Route::delete('project/del/:id', 'v1.study.StudyProject/delete')->option(['real_name' => '删除研学项目']);
    })->option(['parent' => 'study', 'cate_name' => '研学项目']);

    /** 研学套餐 */
    Route::group(function () {
        //套餐列表
        Route::get('package', 'v1.study.StudyPackage/index')->option(['real_name' => '研学套餐列表']);
        //套餐详情
        Route::get('package/:id', 'v1.study.StudyPackage/read')->option(['real_name' => '研学套餐详情']);
        //保存修改套餐
        Route::post('package/:id', 'v1.study.StudyPackage/save')->option(['real_name' => '保存研学套餐']);
        //删除套餐
        Route::delete('package/del/:id', 'v1.study.StudyPackage/delete')->option(['real_name' => '删除研学套餐']);
    })->option(['parent' => 'study', 'cate_name' => '研学套餐']);

    /** 研学抢购场次 */
    Route::group(function () {
        //场次列表
        Route::get('snatch', 'v1.study.StoreStudySnatch/index')->option(['real_name' => '研学抢购场次列表']);
        //场次详情
        Route::get('snatch/:id', 'v1.study.StoreStudySnatch/read')->option(['real_name' => '研学抢购场次详情']);
        //保存修改场次
        Route::post('snatch/:id', 'v1.study.StoreStudySnatch/save')->option(['real_name' => '保存研学抢购场次']);
        //删除场次
        Route::delete('snatch/del/:id', 'v1.study.StoreStudySnatch/delete')->option(['real_name' => '删除研学抢购场次']);
    })->option(['parent' => 'study', 'cate_name' => '抢购场次']);

    /** 研学抢购人（抢购订单） */
    Route::group(function () {
        //抢购人列表
        Route::get('order', 'v1.study.StudyOrder/index')->option(['real_name' => '研学抢购人列表']);
    })->option(['parent' => 'study', 'cate_name' => '抢购人信息']);

})->middleware([
    \app\http\middleware\AllowOriginMiddleware::class,
    \app\adminapi\middleware\AdminAuthTokenMiddleware::class,
    \app\adminapi\middleware\AdminCheckRoleMiddleware::class,
    \app\adminapi\middleware\AdminLogMiddleware::class
])->option(['mark' => 'study', 'mark_name' => '研学模块']);
