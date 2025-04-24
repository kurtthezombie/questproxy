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
import MyReportsView from '@/views/MyReportsView.vue'
import PaymentView from '@/views/PaymentView.vue'
import PaymentHistoryView from '@/views/PaymentHistoryView.vue'
import Payment from '@/components/payment/Payment.vue'
import PaymentSuccess from '@/components/payment/PaymentSuccess.vue'
import PaymentCancel from '@/components/payment/PaymentCancel.vue'
import BookingCard from '@/components/BookingCard.vue'
import ReviewView from '@/views/ReviewView.vue'
import BeforePayment from '@/views/BeforePayment.vue'
import PilotMatchingView from '@/views/PilotMatchingView.vue'
import VerifyPaymentView from '@/views/VerifyPaymentView.vue'

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
      name: 'PaymentHistoryView',
      component: PaymentHistoryView,
    },
    {
      path: '/payment/success/:booking_id',
      name: 'payment-success',
      component: PaymentSuccess,
      meta: { requiresAuth: true }
    },
    {
      path: '/payment/cancel/:bookingId',
      name: 'payment-cancel',
      component: PaymentCancel,
      meta: { requiresAuth: true }
    },
    {
      path: '/bookings/:serviceId',
      name: 'BookingCard',
      component: BookingCard,
    },
    {
      path: '/myportfolios',
      name: 'MyPortfolio',
      component: MyPortfolioView,
      meta: { requiresAuth: true },
    },
    {
      path: '/portfolio/:username',
      name: 'PortfolioView',
      component: PortfolioView,
      meta: { requiresAuth: true },
    },
    {
      path: '/transaction-history',
      name: 'TransactionHistory',
      component: TransactionHistoryView,
      meta: { requiresAuth: true },
    },
    {
      path: '/report/:username',
      name: 'ReportView',
      component: ReportView,
      meta: { requiresAuth: true },
    },
    {
      path: '/myreports',
      name: 'MyReports',
      component: MyReportsView,
      meta: { requiresAuth: true },
    },
    {
      path: '/review',
      name: 'Review',
      component: ReviewView,
    },
    {
      path: '/before-payment',
      name: 'BeforePayment',
      component: BeforePayment,
    },
    {
      path: '/pilot-matching',
      name: 'PilotMatching',
      component: PilotMatchingView,
      meta: { requiresAuth: true },
    },
    {
      path: '/verify-payment/:id',
      name: 'VerifyPayment',
      component: VerifyPaymentView,
      meta: { requiresAuth: true },
    },
  ]
});

router.beforeEach((to, from, next) => {
  const isAuthenticated = !!localStorage.getItem('authToken');
  const userRole = localStorage.getItem('userRole');

  const guestOnlyRoutes = ['login', 'signup', 'home'];

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
    if (isAuthenticated && guestOnlyRoutes.includes(to.name)) {
      next({ name: 'homepage' }); // or wherever you want to redirect logged-in users
    } else {
      next();
    }
  }
});

export default router;