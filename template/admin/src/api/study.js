// +----------------------------------------------------------------------
// | CRMEB [ CRMEB赋能开发者，助力企业发展 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2026 https://www.crmeb.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed CRMEB并不是自由软件，未经许可不能去掉CRMEB相关版权
// +----------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +----------------------------------------------------------------------

import request from '@/libs/request';

/**
 * @description 研学讲师 -- 列表
 */
export function lecturerListApi(data) {
  return request({
    url: 'study/lecturer',
    method: 'get',
    params: data,
  });
}

/**
 * @description 研学讲师 -- 全部启用的讲师（下拉选择）
 */
export function lecturerAllApi() {
  return request({
    url: 'study/lecturer/all',
    method: 'get',
  });
}

/**
 * @description 研学讲师 -- 详情
 */
export function lecturerInfoApi(id) {
  return request({
    url: `study/lecturer/${id}`,
    method: 'get',
  });
}

/**
 * @description 研学讲师 -- 保存/修改
 */
export function lecturerSaveApi(id, data) {
  return request({
    url: `study/lecturer/${id}`,
    method: 'post',
    data,
  });
}

/**
 * @description 研学讲师 -- 删除
 */
export function lecturerDelApi(id) {
  return request({
    url: `study/lecturer/del/${id}`,
    method: 'DELETE',
  });
}

/**
 * @description 研学抢购场次 -- 列表
 */
export function storeSnatchListApi(data) {
  return request({
    url: 'study/snatch',
    method: 'get',
    params: data,
  });
}

/**
 * @description 研学抢购场次 -- 详情
 */
export function storeSnatchInfoApi(id) {
  return request({
    url: `study/snatch/${id}`,
    method: 'get',
  });
}

/**
 * @description 研学抢购场次 -- 保存/修改
 */
export function storeSnatchSaveApi(id, data) {
  return request({
    url: `study/snatch/${id}`,
    method: 'post',
    data,
  });
}

/**
 * @description 研学抢购场次 -- 删除
 */
export function storeSnatchDelApi(id) {
  return request({
    url: `study/snatch/del/${id}`,
    method: 'DELETE',
  });
}

/**
 * @description 研学抢购人 -- 列表
 */
export function studyOrderListApi(data) {
  return request({
    url: 'study/order',
    method: 'get',
    params: data,
  });
}

/**
 * @description 研学套餐 -- 列表
 */
export function studyPackageListApi(data) {
  return request({
    url: 'study/package',
    method: 'get',
    params: data,
  });
}

/**
 * @description 研学套餐 -- 详情
 */
export function studyPackageInfoApi(id) {
  return request({
    url: `study/package/${id}`,
    method: 'get',
  });
}

/**
 * @description 研学套餐 -- 保存/修改
 */
export function studyPackageSaveApi(id, data) {
  return request({
    url: `study/package/${id}`,
    method: 'post',
    data,
  });
}

/**
 * @description 研学套餐 -- 删除
 */
export function studyPackageDelApi(id) {
  return request({
    url: `study/package/del/${id}`,
    method: 'DELETE',
  });
}

/**
 * @description 研学项目 -- 列表
 */
export function studyProjectListApi(data) {
  return request({
    url: 'study/project',
    method: 'get',
    params: data,
  });
}

/**
 * @description 研学项目 -- 详情
 */
export function studyProjectInfoApi(id) {
  return request({
    url: `study/project/${id}`,
    method: 'get',
  });
}

/**
 * @description 研学项目 -- 保存/修改
 */
export function studyProjectSaveApi(id, data) {
  return request({
    url: `study/project/${id}`,
    method: 'post',
    data,
  });
}

/**
 * @description 研学项目 -- 删除
 */
export function studyProjectDelApi(id) {
  return request({
    url: `study/project/del/${id}`,
    method: 'DELETE',
  });
}

/**
 * @description 研学服务商 -- 列表
 */
export function serviceProviderListApi(data) {
  return request({
    url: 'study/provider',
    method: 'get',
    params: data,
  });
}

/**
 * @description 研学服务商 -- 全部启用的服务商（下拉选择）
 */
export function serviceProviderAllApi() {
  return request({
    url: 'study/provider/all',
    method: 'get',
  });
}

/**
 * @description 研学服务商 -- 详情
 */
export function serviceProviderInfoApi(id) {
  return request({
    url: `study/provider/${id}`,
    method: 'get',
  });
}

/**
 * @description 研学服务商 -- 保存/修改
 */
export function serviceProviderSaveApi(id, data) {
  return request({
    url: `study/provider/${id}`,
    method: 'post',
    data,
  });
}

/**
 * @description 研学服务商 -- 删除
 */
export function serviceProviderDelApi(id) {
  return request({
    url: `study/provider/del/${id}`,
    method: 'DELETE',
  });
}

/**
 * @description 研学供应商账号 -- 列表
 */
export function providerAccountListApi(data) {
  return request({
    url: 'study/provider_account',
    method: 'get',
    params: data,
  });
}

/**
 * @description 研学供应商账号 -- 详情
 */
export function providerAccountInfoApi(id) {
  return request({
    url: `study/provider_account/${id}`,
    method: 'get',
  });
}

/**
 * @description 研学供应商账号 -- 保存/修改
 */
export function providerAccountSaveApi(id, data) {
  return request({
    url: `study/provider_account/${id}`,
    method: 'post',
    data,
  });
}

/**
 * @description 研学供应商账号 -- 删除
 */
export function providerAccountDelApi(id) {
  return request({
    url: `study/provider_account/del/${id}`,
    method: 'DELETE',
  });
}
