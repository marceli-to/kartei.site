import { createRouter, createWebHistory } from 'vue-router';
import Home from '@/views/Home.vue';
import Settings from '@/views/settings/Index.vue';
import Archives from '@/views/archives/Index.vue';
import ArchiveSettings from '@/views/archive/Index.vue';
import ArchiveRecords from '@/views/records/Index.vue';

// Error pages
import Error401 from '@/views/error/401.vue';
import Error403 from '@/views/error/403.vue';
import Error404 from '@/views/error/404.vue';
import Error419 from '@/views/error/419.vue';

// Development
import Users from '@/views/Users.vue';
// import Archives from '@/views/Archives.vue';
import Icons from '@/views/ui/Icons.vue';
import Fonts from '@/views/ui/Fonts.vue';
import Elements from '@/views/ui/Elements.vue';
import Colors from '@/views/ui/Colors.vue';
import Layouts from '@/views/ui/Layouts.vue';

const routes = [
  { path: '/archiv', name: 'home', component: Home },
  { path: '/archiv/einstellungen', name: 'settings', component: Settings },
  { path: '/archiv/karteien', name: 'archives', component: Archives },
  { path: '/archiv/kartei/karten/:uuid', name: 'archiveRecords', component: ArchiveRecords },
  { path: '/archiv/kartei/einstellungen/:uuid?', name: 'archiveSettings', component: ArchiveSettings },

  // Error pages
  { path: '/error/401', name: '401', component: Error401 },
  { path: '/error/403', name: '403', component: Error403 },
  { path: '/error/404', name: '404', component: Error404 },
  { path: '/error/419', name: '419', component: Error419 },
  { path: '/:catchAll(.*)', redirect: '/error/404' },

  // ui/dev stuff
  { path: '/archiv/benutzer', name: 'users', component: Users },
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