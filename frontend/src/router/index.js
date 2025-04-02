import { createRouter, createWebHistory } from 'vue-router'
import Dashboard from '@/components/Dashboard.vue'
import HomeView from '../views/HomeView.vue'
import LoginView from '@/views/LoginView.vue'
import ReportView from '@/views/ReportView.vue'
import ServiceView from '@/views/ServiceView.vue'
import RegisterView from '../views/RegisterView.vue'
import HomepageView from '@/views/HomePageView.vue';
import PortfolioView from '@/views/PortfolioView.vue'
import UserProfileView from '@/views/UserProfileView.vue'
import EditServiceView from '@/views/EditServiceView.vue'
import ServicesHistory from '@/views/ServicesHistory.vue'
import MyPortfolioView from '@/views/MyPortfolioView.vue'
import LeaderboardsView from '@/views/LeaderboardsView.vue'
import CreateServiceView from '@/views/CreateServiceView.vue'
import ConfirmDeleteUser from '@/views/ConfirmDeleteUser.vue'
import NotificationsTest from '@/views/NotificationsTest.vue'
import AccountSettingview from '@/views/AccountSettingView.vue'
import OtpEmailVerification from '@/views/OtpEmailVerification.vue'
import TransactionHistoryView from '@/views/TransactionHistoryView.vue'

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
    {
      path: '/create-service',
      name: 'create-service',
      component: CreateServiceView,
    },

    {
      path: '/users/:username',
      name: 'userprofile',
      component: UserProfileView, 
    },
    {
      path: '/otp-verification',
      name: 'otp',
      component: OtpEmailVerification, 
    },
    {
      path: '/services/:title',
      name: 'ServiceView',
      component: ServiceView
    },
    {
      path: '/serviceshistory',
      name: 'ServicesHistory',
      component: ServicesHistory
    },
    {
      path: '/services/:id/edit',
      name: 'editService',
      component: EditServiceView,
      meta: { requiresAuth: true }
    },
    {
      path: '/notifications/test',
      name: 'notifTest',
      component: NotificationsTest
    },
    {
      path: '/myportfolios',
      name: 'MyPortfolio',
      component: MyPortfolioView,
    },
    {
      path: '/portfolio/:username',
      name: 'PortfolioView',
      component: PortfolioView,
    },
    {
      path: '/transaction-history',
      name: 'TransactionHistory',
      component: TransactionHistoryView,
    }
    {
      path: '/report/:username',
      name: 'ReportView',
      component: ReportView,
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
