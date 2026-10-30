import axios from 'axios';
import { Message } from 'element-ui';
import Setting from '@/setting';
import { getToken, removeToken } from '@/libs/util';
import router from '@/router';

const service = axios.create({
  baseURL: Setting.apiBaseURL,
  timeout: 100000,
});

// 请求拦截器
service.interceptors.request.use(
  (config) => {
    const token = getToken();
    if (token) {
      config.headers['Authori-zation'] = 'Bearer ' + token;
    }
    return config;
  },
  (error) => Promise.reject(error)
);

// 响应拦截器
service.interceptors.response.use(
  (response) => {
    const res = response.data || {};
    if (res.status === 200) {
      return res;
    }
    if (res.status === 401 || res.status === 402) {
      removeToken();
      router.replace({ name: 'login' }).catch(() => {});
      Message.error(res.msg || '登录已过期，请重新登录');
      return Promise.reject(res);
    }
    Message.error(res.msg || '请求失败');
    return Promise.reject(res);
  },
  (error) => {
    Message.error('网络错误');
    return Promise.reject(error);
  }
);

export default service;
