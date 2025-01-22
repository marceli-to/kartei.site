import { createApp } from 'vue';
import { createPinia } from 'pinia';
import App from './App.vue';
import router from './router';

const pinia = createPinia();
if (document.getElementById('app')) {
  createApp(App)
    .use(router)
    .use(pinia)
    .mount('#app');
}