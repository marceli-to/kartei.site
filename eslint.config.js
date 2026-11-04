import js from '@eslint/js';
import pluginVue from 'eslint-plugin-vue';
import prettier from 'eslint-config-prettier';
import globals from 'globals';

// Linting only; formatting is Prettier's job (eslint-config-prettier turns the
// overlapping style rules off). npm run lint / npm run format
export default [
  {
    ignores: ['public/**', 'vendor/**', 'node_modules/**', 'storage/**', 'bootstrap/cache/**'],
  },
  js.configs.recommended,
  ...pluginVue.configs['flat/recommended'],
  prettier,
  {
    files: ['resources/js/**/*.{js,vue}'],
    languageOptions: {
      ecmaVersion: 'latest',
      sourceType: 'module',
      globals: { ...globals.browser },
    },
    rules: {
      // Single-word names are the convention here (Slide, Dialog, Icon, ...)
      'vue/multi-word-component-names': 'off',
    },
  },
  {
    files: ['*.config.js', 'eslint.config.js'],
    languageOptions: { globals: { ...globals.node } },
  },
];
