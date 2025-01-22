import { createApp } from 'vue';
import Dashboard from './Dashboard.vue';
if (document.getElementById('dashboard')) {
  createApp(Dashboard).mount('#dashboard');
}