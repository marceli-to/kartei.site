import { defineStore } from 'pinia';

/**
 * State behind the info (lightbulb) icon in the header.
 *
 * Views with an info box register while they are visible (see useInfoBox).
 * The icon can only be toggled while at least one is registered, and the
 * box is open only then. A view registering with `auto` opens it right away,
 * e.g. when something essential is still missing.
 */
export const useInfoIconStore = defineStore('infoIcon', {
  state: () => ({
    sources: {},
    open: false,
  }),

  getters: {
    isAvailable: (state) => Object.keys(state.sources).length > 0,
    isOpen() {
      return this.open && this.isAvailable;
    },
  },

  actions: {
    register(id, auto) {
      const isNew = !(id in this.sources);
      this.sources[id] = true;
      // A view that just appeared starts with its own default
      if (isNew || auto) {
        this.open = auto;
      }
    },

    unregister(id) {
      delete this.sources[id];
      if (!this.isAvailable) {
        this.open = false;
      }
    },

    toggle() {
      if (this.isAvailable) {
        this.open = !this.open;
      }
    },

    close() {
      this.open = false;
    },
  },
});
