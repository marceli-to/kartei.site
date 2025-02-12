import { createRouter, createWebHistory } from 'vue-router';
import Home from '@/views/Home.vue';
import Users from '@/views/Users.vue';
import Archives from '@/views/Archives.vue';
import Icons from '@/views/ui/Icons.vue';
import Fonts from '@/views/ui/Fonts.vue';
import Elements from '@/views/ui/Elements.vue';
import Colors from '../views/ui/Colors.vue';

const routes = [
  { path: '/archiv', name: 'Home', component: Home },
  { path: '/archiv/benutzer', name: 'Users', component: Users },
  { path: '/archiv/karteien', name: 'Archives', component: Archives },

  // ui/dev stuff
  { path: '/archiv/icons', name: 'Icons', component: Icons },
  { path: '/archiv/fonts', name: 'Fonts', component: Fonts },
  { path: '/archiv/elements', name: 'Elements', component: Elements },
  { path: '/archiv/colors', name: 'Colors', component: Colors },
];

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes,
});

export default router;