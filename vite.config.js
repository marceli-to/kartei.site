import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { resolve } from 'path';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
  resolve: {
    alias: {
      img: resolve('resources/media'),
      fonts: resolve('resources/fonts'),
      vue: 'vue/dist/vue.esm-bundler.js'
    }
  },
  plugins: [
    laravel({
      input: ['resources/css/app.css', 'resources/js/app.js'],
      refresh: true,
    }),
    vue({
      template: {
          transformAssetUrls: {
              base: null,
              includeAbsolute: false,
          },
      },
    }),
  ],
});
