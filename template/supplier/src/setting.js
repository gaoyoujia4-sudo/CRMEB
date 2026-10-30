// 请求接口地址，未配置则自动使用当前域名下的 /supplierapi
const VUE_APP_API_URL = process.env.VUE_APP_API_URL || `${location.origin}/supplierapi`;

const Setting = {
  // 接口请求地址
  apiBaseURL: VUE_APP_API_URL,
  // 路由模式：hash，避免子目录部署需要服务端 rewrite
  routerMode: 'hash',
};

export default Setting;
