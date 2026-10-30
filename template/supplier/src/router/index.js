import Vue from 'vue';
import Router from 'vue-router';
import Setting from '@/setting';
import { getToken } from '@/libs/util';

Vue.use(Router);

const routes = [
  {
    path: '/login',
    name: 'login',
    component: () => import('@/pages/login'),
  },
  {
    path: '/project',
    name: 'project',
    component: () => import('@/pages/project'),
    meta: { requiresAuth: true },
  },
  {
    path: '/',
    redirect: '/project',
  },
  {
    path: '*',
    redirect: '/project',
  },
];

const router = new Router({
  mode: Setting.routerMode,
  routes,
});

router.beforeEach((to, from, next) => {
  if (to.meta.requiresAuth && !getToken()) {
    next({ name: 'login' });
  } else {
    next();
  }
});

export default router;
