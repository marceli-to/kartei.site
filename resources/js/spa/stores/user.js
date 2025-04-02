import { defineStore } from 'pinia';
import { getUserPermissions } from '@/services/api/user';

export const useUserStore = defineStore('user', {
  state: () => ({
    user: {},
    permissions: typeof window !== 'undefined' ? window.permissions || [] : [],
    roles: typeof window !== 'undefined' ? window.roles || [] : [],
    initialized: false
  }),

  getters: {
    isSuperAdmin: (state) => {
      return state.roles.some(role => role.name === 'Super Admin');
    }
  },

  actions: {
    async initialize() {
      if (this.initialized) return;

      try {
        await this.fetchPermissions();
      } catch (error) {
        console.error('Initialization error:', error);
      } finally {
        this.initialized = true;
      }
    },

    async fetchPermissions() {
      try {
        const permissions = await getUserPermissions();
        this.user = permissions.user;
        this.permissions = permissions.permissions;
        this.roles = permissions.roles;
      } catch (error) {
        console.error('Failed to fetch permissions and roles:', error);
      }
    },

    can(permission) {
      if (this.isSuperAdmin) return true;
      return this.permissions.some(p => p.name === permission);
    },
  }
});
