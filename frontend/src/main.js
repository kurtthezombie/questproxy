//import './assets/main.css'
import './assets/css/style.css'

import { createApp } from 'vue'
import { LoadingPlugin } from 'vue-loading-overlay'
import { createPinia } from 'pinia'
import App from './App.vue'
import router from './router'
import 'vue-loading-overlay/dist/css/index.css';

const app = createApp(App)

app.use(createPinia());
app.use(LoadingPlugin);
app.use(router)

app.mount('#app')
