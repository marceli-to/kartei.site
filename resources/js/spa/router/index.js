import { createRouter, createWebHistory } from 'vue-router';
import Home from '@/views/Home.vue';
import Users from '@/views/Users.vue';
import Archives from '@/views/Archives.vue';
import Components from '@/views/Components.vue';

const routes = [
  { path: '/archiv', name: 'Home', component: Home },
  { path: '/archiv/benutzer', name: 'Users', component: Users },
  { path: '/archiv/karteien', name: 'Archives', component: Archives },
  { path: '/archiv/components', name: 'Components', component: Components },
];

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes,
});

export default router;