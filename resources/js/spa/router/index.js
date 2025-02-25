import { createRouter, createWebHistory } from 'vue-router';
import Home from '@/views/Home.vue';
import Settings from '@/views/settings/Index.vue';
import Users from '@/views/Users.vue';
import Archives from '@/views/Archives.vue';
import Icons from '@/views/ui/Icons.vue';
import Fonts from '@/views/ui/Fonts.vue';
import Elements from '@/views/ui/Elements.vue';
import Colors from '@/views/ui/Colors.vue';
import Layouts from '@/views/ui/Layouts.vue';

const routes = [
  { path: '/archiv', name: 'home', component: Home },
  { path: '/archiv/einstellungen', name: 'settings', component: Settings },
  { path: '/archiv/benutzer', name: 'users', component: Users },
  { path: '/archiv/karteien', name: 'archives', component: Archives },

  // ui/dev stuff
  { path: '/archiv/icons', name: 'icons', component: Icons },
  { path: '/archiv/fonts', name: 'fonts', component: Fonts },
  { path: '/archiv/elements', name: 'elements', component: Elements },
  { path: '/archiv/colors', name: 'colors', component: Colors },
  { path: '/archiv/layouts', name: 'layouts', component: Layouts },
];

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes,
});

export default router;