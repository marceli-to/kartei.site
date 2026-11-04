import { createApp } from 'vue';
import { createPinia } from 'pinia';
import App from '@/App.vue';
import router from '@/router';
import { registerApiErrorRouter } from '@/services/api/error';

const app = createApp(App);
const pinia = createPinia();

// proceed only if #app is present
if (document.querySelector('#app')) {
  // Pinia first: the router guard reads the user store on the first navigation
  app.use(pinia);
  app.use(router);
  registerApiErrorRouter(router);
  app.mount('#app');
}
