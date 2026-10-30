// +----------------------------------------------------------------------
// | CRMEB [ CRMEB赋能开发者，助力企业发展 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2026 https://www.crmeb.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed CRMEB并不是自由软件，未经许可不能去掉CRMEB相关版权
// +----------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +----------------------------------------------------------------------
module.exports = {
  // 打包产物输出到后端 public/supplier，访问地址 /supplier/index.html
  outputDir: '../../crmeb/public/supplier',
  // 相对路径，便于放到子目录直接访问
  publicPath: './',
  runtimeCompiler: true,
  productionSourceMap: false,
  lintOnSave: false,
  devServer: {
    port: 8081,
    open: false,
  },
};
