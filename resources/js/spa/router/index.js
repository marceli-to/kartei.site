import { createRouter, createWebHistory } from 'vue-router';
import { useUserStore } from '@/stores/user';

// Views are loaded on demand, each in its own chunk.
//
// meta.archiveAbility: the route's :uuid is an archive uuid and the user
// needs one of these ArchivePolicy abilities for it (see stores/user.js).
// meta.permission: a global permission (account level).
const ARCHIVE_SETTINGS_ABILITIES = [
  'update',
  'manageStructure',
  'manageTemplate',
  'manageTags',
  'manageUsers',
  'delete',
];

const routes = [
  { path: '/archiv', name: 'home', component: () => import('@/views/Home.vue') },
  { path: '/archiv/einstellungen', name: 'settings', component: () => import('@/views/settings/Index.vue') },
  { path: '/archiv/meine-karteien', name: 'archives', component: () => import('@/views/archives/Index.vue') },
  {
    path: '/archiv/meine-karteien/duplizieren',
    name: 'archivesDuplicate',
    component: () => import('@/views/archives/Duplicate.vue'),
    meta: { permissionWithoutUuid: 'create.archive' },
  },
  {
    path: '/archiv/kartei/einstellungen/:uuid?',
    name: 'archiveSettings',
    component: () => import('@/views/archive/Index.vue'),
    // Without uuid this page creates a new archive
    meta: { archiveAbility: ARCHIVE_SETTINGS_ABILITIES, permissionWithoutUuid: 'create.archive' },
  },
  {
    path: '/archiv/kartei/karten/:uuid',
    name: 'archiveRecords',
    component: () => import('@/views/records/Index.vue'),
    meta: { archiveAbility: 'view' },
  },
  {
    path: '/archiv/kartei/karte/erstellen/:uuid',
    name: 'archiveRecordCreate',
    component: () => import('@/views/records/RecordForm.vue'),
    meta: { archiveAbility: 'createRecord' },
  },
  // :uuid is the record's uuid here; the view checks the ability once the record is loaded
  {
    path: '/archiv/kartei/karte/bearbeiten/:uuid',
    name: 'archiveRecordUpdate',
    component: () => import('@/views/records/RecordForm.vue'),
  },

  // Error pages
  ...[401, 403, 404].map((status) => ({
    path: `/error/${status}`,
    name: String(status),
    component: () => import('@/views/error/ErrorPage.vue'),
    props: { status },
  })),
  { path: '/:catchAll(.*)', redirect: '/error/404' },
];

const router = createRouter({
  // No base: import.meta.env.BASE_URL is the asset path (/build/) with laravel-vite-plugin
  history: createWebHistory(),
  routes,
});

// Load permissions before the first view renders, then check route access
router.beforeEach(async (to) => {
  const userStore = useUserStore();
  await userStore.initialize();

  const { archiveAbility, permissionWithoutUuid } = to.meta;
  const uuid = to.params.uuid;

  if (permissionWithoutUuid && !uuid) {
    return userStore.can(permissionWithoutUuid) || { name: '403' };
  }

  if (archiveAbility && uuid) {
    return userStore.canArchive(uuid, archiveAbility) || { name: '403' };
  }

  return true;
});

export default router;
