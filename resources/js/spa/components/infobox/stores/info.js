// src/stores/info.js
import { defineStore } from 'pinia';

export const useInfoIconStore = defineStore('infoIcon', {
  state: () => ({
    isActive: false
  }),
  
  actions: {
    toggle() {
      this.isActive = !this.isActive;
    },
    
    setActive(status) {
      this.isActive = status;
    }
  }
});