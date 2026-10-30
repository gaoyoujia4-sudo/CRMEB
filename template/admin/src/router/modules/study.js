// +----------------------------------------------------------------------
// | CRMEB [ CRMEB赋能开发者，助力企业发展 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2026 https://www.crmeb.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed CRMEB并不是自由软件，未经许可不能去掉CRMEB相关版权
// +----------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +----------------------------------------------------------------------

import LayoutMain from '@/layout';
import setting from '@/setting';
let routePre = setting.routePre;

const pre = 'study_';

export default {
  path: routePre + '/study',
  name: 'study',
  header: 'study',
  meta: {
    title: '研学',
    // 授权标识
    auth: ['admin-study'],
  },
  redirect: {
    name: `${pre}lecturer`,
  },
  component: LayoutMain,
  children: [
    {
      path: 'lecturer',
      name: `${pre}lecturer`,
      meta: {
        title: '讲师',
        auth: ['admin-study-lecturer'],
      },
      component: () => import('@/pages/study/lecturer'),
    },
    {
      path: 'study_project',
      name: `${pre}studyProject`,
      meta: {
        title: '研学项目',
        auth: ['admin-study-study-project'],
      },
      component: () => import('@/pages/study/studyProject'),
    },
    {
      path: 'service_provider',
      name: `${pre}serviceProvider`,
      meta: {
        title: '服务商',
        auth: ['admin-study-service-provider'],
      },
      component: () => import('@/pages/study/serviceProvider'),
    },
    {
      path: 'service_provider_account',
      name: `${pre}serviceProviderAccount`,
      meta: {
        title: '供应商账号',
        auth: ['admin-study-service-provider-account'],
      },
      component: () => import('@/pages/study/serviceProviderAccount'),
    },
    {
      path: 'study_package',
      name: `${pre}studyPackage`,
      meta: {
        title: '研学套餐',
        auth: ['admin-study-study-package'],
      },
      component: () => import('@/pages/study/studyPackage'),
    },
    {
      path: 'store_snatch',
      name: `${pre}storeSnatch`,
      meta: {
        title: '抢购场次',
        auth: ['admin-study-store-snatch'],
      },
      component: () => import('@/pages/study/storeSnatch'),
    },
    {
      path: 'study_order',
      name: `${pre}studyOrder`,
      meta: {
        title: '抢购人信息',
        auth: ['admin-study-study-order'],
      },
      component: () => import('@/pages/study/studyOrder'),
    },
  ],
};
