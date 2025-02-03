import { createApp } from 'vue';
import { createPinia } from 'pinia';
import App from './App.vue';
import router from './router';

const app = createApp(App);
const pinia = createPinia();

app.use(router);
app.use(pinia);

async function initializeApp() {
  app.mount('#app');
}

if (document.getElementById('app')) {
  initializeApp();
}