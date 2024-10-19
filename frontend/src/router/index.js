import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import RegisterView from '../views/RegisterView.vue'
import LoginView from '@/views/LoginView.vue'
import Dashboard from '@/components/Dashboard.vue'
import HomepageView from '@/views/HomepageView.vue';
import LeaderboardsView from '@/views/LeaderboardsView.vue'
import ConfirmDeleteUser from '@/views/ConfirmDeleteUser.vue'
import AccountSettingview from '@/views/AccountSettingView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView
    },
    {
      path: '/about',
      name: 'about',
      // route level code-splitting
      // this generates a separate chunk (About.[hash].js) for this route
      // which is lazy-loaded when the route is visited.
      component: () => import('../views/AboutView.vue')
    },
    {
      path: '/signup',
      name: 'signup',
      component: RegisterView
    },
    {
      path: '/login',
      name: 'login',
      component: LoginView
    },
    {
      path: '/dashboard',
      name: 'dashboard',
      component: Dashboard,
      meta: { requiresAuth: true }
    },
    {
      path: '/home',
      name: 'homepage',  
      component: HomepageView,  
      meta: { requiresAuth: true }
    },
    {
      path: '/leaderboards',
      name: 'leaderboards',
      component: LeaderboardsView,
    },
    {
      path: '/confirm-delete',
      name: 'confirm-delete-user',
      component: ConfirmDeleteUser,
    },
    {
      path: '/account-settings',
      name: 'account-settings',
      component: AccountSettingview,
    },
  ]
})

router.beforeEach((to, from, next) => {
  const isAuthenticated = !!localStorage.getItem('authToken');
  const userRole = localStorage.getItem('userRole');

  if (to.matched.some(record => record.meta.requiresAuth)) {
    if (!isAuthenticated) {
      next({ name: 'login' });
    } else {
      if (to.name === 'dashboard') {
        
        if (userRole === 'gamer' || userRole === 'pilot') {
          next({ name: 'home' });
        } else {
          next();
        }
      } else {
        next();
      }
    }
  } else {
    next();
  }
});

export default router
