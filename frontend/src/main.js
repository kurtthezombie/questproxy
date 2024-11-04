//import './assets/main.css'
import './assets/css/style.css'

import { createApp } from 'vue'
import { LoadingPlugin } from 'vue-loading-overlay'
import { createPinia } from 'pinia'
import App from './App.vue'
import router from './router'
import 'vue-loading-overlay/dist/css/index.css';
import piniaPluginPersistedstate from 'pinia-plugin-persistedstate'


const app = createApp(App)

const pinia = createPinia()
pinia.use(piniaPluginPersistedstate)

app.use(pinia);
app.use(LoadingPlugin);
app.use(router)

app.mount('#app')
