import { defineStore } from 'pinia';
import axios from 'axios';

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
        const { data } = await axios.get('/api/user');
        this.user = data.user;
        this.permissions = data.permissions;
        this.roles = data.roles;
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