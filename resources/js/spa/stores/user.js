import { defineStore } from 'pinia';
import { getUserPermissions } from '@/services/api/user';
import { getFavorites } from '@/services/api/favorite';
import { notifyError } from '@/services/api/error';

let initializing = null;

export const useUserStore = defineStore('user', {
  state: () => ({
    user: {},
    permissions: [],
    roles: [],
    // Per-archive abilities from ArchivePolicy: { [archiveUuid]: ['view', 'updateRecord', ...] }
    archives: {},
    favorites: [],
    initialized: false,
  }),

  getters: {
    isSuperAdmin: (state) => {
      return state.roles.some((role) => role.name === 'Super Admin');
    },
  },

  actions: {
    // App.vue and the router guard both call this on load; share one run
    initialize() {
      if (this.initialized) return Promise.resolve();
      if (!initializing) {
        initializing = (async () => {
          // Only counts as initialized once the permissions are in: after a failed
          // request the next navigation tries again instead of showing 403 for good
          if (!(await this.fetchPermissions())) return;
          this.initialized = true;
          await this.fetchFavorites();
        })().finally(() => {
          initializing = null;
        });
      }
      return initializing;
    },

    // Returns whether the permissions were loaded
    async fetchPermissions() {
      try {
        const permissions = await getUserPermissions();
        this.user = permissions.user;
        this.permissions = permissions.permissions;
        this.roles = permissions.roles;
        this.archives = permissions.archives || {};
        return true;
      } catch (error) {
        notifyError(error, 'Ihre Berechtigungen konnten nicht geladen werden.');
        return false;
      }
    },

    async fetchFavorites() {
      try {
        const favorites = await getFavorites();
        this.favorites = favorites;
      } catch {
        // Not worth a message: the hearts just show as empty
      }
    },

    can(permission) {
      if (this.isSuperAdmin) return true;
      return this.permissions.some((p) => p.name === permission);
    },

    // Same rules as the API (ArchivePolicy). abilities: one name or a list (any of them)
    canArchive(archiveUuid, abilities) {
      if (this.isSuperAdmin) return true;
      const granted = this.archives[archiveUuid] || [];
      return [].concat(abilities).some((ability) => granted.includes(ability));
    },
  },
});
