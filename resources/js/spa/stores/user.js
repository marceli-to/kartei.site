import { defineStore } from 'pinia';
import { getUserPermissions } from '@/services/api/user';
import { getFavorites } from '@/services/api/favorite';

export const useUserStore = defineStore('user', {
  state: () => ({
    user: {},
    permissions: typeof window !== 'undefined' ? window.permissions || [] : [],
    roles: typeof window !== 'undefined' ? window.roles || [] : [],
    favorites: [],
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
        await this.fetchFavorites();
      } 
      catch (error) {
        console.error('Initialization error:', error);
      } 
      finally {
        this.initialized = true;
      }
    },

    async fetchPermissions() {
      try {
        const permissions = await getUserPermissions();
        this.user = permissions.user;
        this.permissions = permissions.permissions;
        this.roles = permissions.roles;
      } 
      catch (error) {
        console.error('Failed to fetch permissions and roles:', error);
      }
    },

    async fetchFavorites() {
      try {
        const favorites = await getFavorites();
        this.favorites = favorites;
      } 
      catch (error) {
        console.error('Failed to fetch favorites:', error);
      }
    },

    can(permission) {
      if (this.isSuperAdmin) return true;
      return this.permissions.some(p => p.name === permission);
    },
  }
});
