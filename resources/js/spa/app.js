import { createApp } from 'vue';
import { createPinia } from 'pinia';
import App from '@/App.vue';
import router from '@/router';

const app = createApp(App);
const pinia = createPinia();

// proceed only if #map is present
if (document.querySelector('#map')) {
  app.use(router)
  app.use(pinia)
  app.mount('#app')
}