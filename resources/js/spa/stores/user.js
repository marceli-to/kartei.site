import { defineStore } from 'pinia';
import axios from 'axios';
import { getUserPermissions } from '@/services/api/user';


export const useUserStore = defineStore('user', {
  state: () => ({
    user: {},
    permissions: window.permissions || [],
    roles: window.roles || [],
    initialized: false
  }),
  
  actions: {
    async initialize() {
      if (!this.initialized) {
        await this.fetchPermissions();
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
    
    can(permission) {
      if (this.roles.some(r => r.name === 'Super Admin')) {
        return true;
      }
      return this.permissions.some(p => p.name === permission);
    },
  }
});