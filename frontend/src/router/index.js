import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import RegisterView from '../views/RegisterView.vue'
import LoginView from '@/views/LoginView.vue'
import Dashboard from '@/components/Dashboard.vue'
import GamerView from '@/views/GamerView.vue'
import GamePilotView from '@/views/GamePilotView.vue'
import LeaderboardsView from '@/views/LeaderboardsView.vue'


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
      path: '/gamer',
      name: 'gamer',
      component: GamerView,
      meta: { requiresAuth: true }
    },
    {
      path: '/game-pilot',
      name: 'game-pilot',
      component: GamePilotView,
      meta: { requiresAuth: true }
    },
    {
      path: '/leaderboards',
      name: 'leaderboards',
      component: LeaderboardsView,
    },
  ]
})

router.beforeEach((to, from, next) => {
  const isAuthenticated = !!localStorage.getItem('authToken');

  if (to.matched.some(record => record.meta.requiresAuth)) {
    if (!isAuthenticated) {
      next({ name: 'login'});
    } else {
      next();
    }
  } else {
    next();
  }
})

export default router
