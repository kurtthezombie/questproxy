import { createRouter, createWebHistory } from 'vue-router';
import HomeView from '../views/HomeView.vue';
import AboutView from '../views/AboutView.vue';
import RegistrationForm from '../views/Registerview.vue'
import LoginForm from '../views/Loginview.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'Home',
      component: HomeView
    },
    {
      path: '/about',
      name: 'About',
      component: AboutView
    },
    {
      path: '/register',
      name: 'Register',
      component: RegistrationForm
    },
    {
      path: '/login',
      name: 'Login',
      component: LoginForm
    }
  ]
});

export default router;