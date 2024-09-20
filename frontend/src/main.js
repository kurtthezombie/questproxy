import Vue from 'vue'
import App from './App.vue'
import './assets/tailwind.css'
import VueRouter from 'vue-router'
import Routes from './routes'
import VueResource from 'vue-resource'
import { store } from "./store/store";

Vue.use(VueResource)
Vue.use(VueRouter)

// Intitiate router instance
export const router = new VueRouter({
  mode: 'history',
  routes: Routes
});

router.beforeEach((to, from, next) => {
  // redirect to login page if not logged in and trying to access a restricted page
  const publicPages = ['/login', '/registration'];
  const authRequired = !publicPages.includes(to.path);
  const loggedIn = localStorage.getItem('user');

  if (authRequired && !loggedIn) {
    return next('/login');
  }

  if (publicPages.includes(to.path) && loggedIn) {
    return next('/');
  }

  next();
})

Vue.config.productionTip = false

new Vue({
  store: store,
  router: router,
  render: h => h(App),
}).$mount('#app')
