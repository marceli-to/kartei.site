import { createApp } from 'vue';
import { createPinia } from 'pinia';
import App from './App.vue';
import router from './router';

const app = createApp(App);
const pinia = createPinia();

app.use(router);
app.use(pinia);

async function initializeApp() {
  // Import and initialize the permission store so we load the permissions before mounting the app
  const { usePermissionStore } = await import('./store/permissions');
  const permissionStore = usePermissionStore();
  await permissionStore.initialize();
  
  // Mount the app after initialization
  app.mount('#app');
}

if (document.getElementById('app')) {
  initializeApp();
}