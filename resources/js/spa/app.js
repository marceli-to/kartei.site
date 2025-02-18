import { createApp } from 'vue';
import { createPinia } from 'pinia';
import App from '@/App.vue';
import router from '@/router';

const app = createApp(App);
const pinia = createPinia();

// proceed only if #app is present
if (document.querySelector('#app')) {
  app.use(router)
  app.use(pinia)
  app.mount('#app')
}