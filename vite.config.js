import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { resolve } from 'path';
import vue from '@vitejs/plugin-vue';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
  resolve: {
    alias: {
      img: resolve('resources/media'),
      fonts: resolve('resources/fonts'),
      '@': resolve('resources/js/spa'),
    }
  },
  server: {
    cors: true,
    headers: {
      'Access-Control-Allow-Origin': 'https://kartei.site.test'
    }
  },
  plugins: [
    tailwindcss(),
    laravel({
      input: ['resources/css/app.css', 'resources/js/app.js', 'resources/js/spa.js'],
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
  ]
});
