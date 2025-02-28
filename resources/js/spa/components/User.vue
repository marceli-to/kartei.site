<template>
  <div class="fixed left-5 bottom-5 z-50 rounded-md bg-white dark:bg-black border border-black dark:border-white px-8 py-6 text-xs leading-snug flex gap-x-8">
    <a href="javascript:;" @click="toggleUserMenu" :class="{'mt-2': hasMenu}">
      <IconSettings />
    </a>
    <div v-if="hasMenu">
      {{ userStore.user.firstname }} {{ userStore.user.name }}, 
      {{ userStore.user.email }},
      {{ userStore.roles.map(role => role.name).join(', ') }} – 
      <a href="/logout">Logout</a>
      <nav class="border-t border-graphite mt-8 pt-8 pb-4 flex justify-between">
        <ul class="flex gap-x-8 list-none p-0">
          <li>
            <router-link to="/archiv/karteien">Karteien</router-link>
          </li>
          <li>
            <router-link to="/archiv/benutzer">Users</router-link>
          </li>
          <li>
            <router-link to="/archiv/icons">Icons</router-link>
          </li>
          <li>
            <router-link to="/archiv/fonts">Fonts</router-link>
          </li>
          <li>
            <router-link to="/archiv/colors">Colors</router-link>
          </li>
          <li>
            <router-link to="/archiv/elements">Elements</router-link>
          </li>
          <li>
            <router-link :to="{ name: 'layouts' }">
              Layouts
            </router-link>
          </li>
        </ul>
      </nav>
    </div>
  </div>
</template>
<script setup>
import { ref } from 'vue'
import IconSettings from '@/components/icons/Settings.vue';
import { useUserStore } from '@/stores/user'
const userStore = useUserStore();

const hasMenu = ref(false);

const toggleUserMenu = () => {
  hasMenu.value = !hasMenu.value
}

</script>