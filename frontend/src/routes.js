import LoginViewVue from "./components/LoginView.vue"
import RegistrationViewVue from "./components/RegistrationView.vue"
import HomeView from './components/HomeView.vue'
import EditViewVue from "./components/EditView.vue"

export default [
    {path: '/', component: HomeView},
    {path: '/login', component: LoginViewVue},
    {path: '/registration', component: RegistrationViewVue},
    {path: '/edit', component: EditViewVue},

    // otherwise redirect to home
    { path: '*', redirect: '/' }
]