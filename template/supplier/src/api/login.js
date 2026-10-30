import request from '@/libs/request';

/**
 * 供应商账号登录
 */
export function supplierLogin(data) {
  return request({
    url: 'login',
    method: 'post',
    data,
  });
}

/**
 * 当前供应商信息
 */
export function supplierInfo() {
  return request({
    url: 'info',
    method: 'get',
  });
}

/**
 * 退出登录
 */
export function supplierLogout() {
  return request({
    url: 'logout',
    method: 'post',
  });
}
