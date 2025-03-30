import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import RegisterView from '../views/RegisterView.vue'
import LoginView from '@/views/LoginView.vue'
import Dashboard from '@/components/Dashboard.vue'
import HomepageView from '@/views/HomePageView.vue';
import LeaderboardsView from '@/views/LeaderboardsView.vue'
import ConfirmDeleteUser from '@/views/ConfirmDeleteUser.vue'
import AccountSettingview from '@/views/AccountSettingView.vue'
import CreateServiceView from '@/views/CreateServiceView.vue'
import UserProfileView from '@/views/UserProfileView.vue'
import OtpEmailVerification from '@/views/OtpEmailVerification.vue'
import ServiceView from '@/views/ServiceView.vue'
import EditServiceView from '@/views/EditServiceView.vue'
import ServicesHistory from '@/views/ServicesHistory.vue'
import PaymentView from '@/views/PaymentView.vue'
import PaymentHistoryView from '@/views/PaymentHistoryView.vue'
import BookingView from '@/components/BookingView.vue'
import PaymentResult from '@/components/PaymentResult.vue' 

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
      path: '/users/:id',
      name: 'userprofile',
      component: UserProfileView,
      meta: { requiresAuth: true }
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
      path: '/services-history',
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
      path: '/payment/:serviceId',
      name: 'PaymentView',
      component: PaymentView,
    },
    {
      path: '/payment-history/',
      name: 'PaymentHistory',
      component: PaymentHistoryView,
    },
    {
      path: '/bookings/:serviceId',
      name: 'BookingView',
      component: BookingView,
    },
    // Add the new route for PaymentResult
    {
      path: '/payment-result',
      name: 'PaymentResult',
      component: PaymentResult,
    },
  ]
});

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

export default router;