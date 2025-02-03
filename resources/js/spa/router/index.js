import { createRouter, createWebHistory } from 'vue-router';
import Home from '@/views/Home.vue';
import Users from '@/views/Users.vue';
import Archives from '@/views/Archives.vue';
import Playground from '@/views/Playground.vue';

const routes = [
  { path: '/archiv', name: 'Home', component: Home },
  { path: '/archiv/benutzer', name: 'Users', component: Users },
  { path: '/archiv/karteien', name: 'Archives', component: Archives },
  { path: '/archiv/playground', name: 'Playground', component: Playground },
];

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes,
});

export default router;