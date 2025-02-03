import { defineStore } from 'pinia';
import axios from 'axios';

export const usePermissionStore = defineStore('permissions', {
  state: () => ({
    permissions: window.permissions || [],
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
        this.permissions = data.data.permissions;
      } catch (error) {
        console.error('Failed to fetch permissions:', error);
      }
    },
    
    can(permission) {
      return this.permissions.some(p => p.name === permission);
    }
  }
});