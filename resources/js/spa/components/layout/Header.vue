<template>
  <header class="relative dark:bg-black z-90">
    <div class="min-h-80 flex border-b border-b-black dark:border-b-white">
      <div class="w-full flex leading-none">
        <div class="w-2/12 min-w-nav">
          <router-link 
            :to="{ name: 'home' }" 
            class="block mt-25" 
            @click="hide">
            <IconLogo class="text-black dark:text-white" />
          </router-link>
        </div>
        <div class="w-7/12">
          <h1 class="font-otto-regular text-lg mt-22 ml-8">
            {{ title }}
          </h1>
        </div>
        <div class="w-3/12">
          <div class="flex justify-end gap-x-48">
            <button 
              type="button" 
              class="mt-20 group disabled:cursor-default"
              :disabled="!infoIcon.isAvailable"
              :aria-pressed="infoIcon.isOpen"
              aria-label="Info"
              @click="infoIcon.toggle">
              <!-- Same 48px box for both variants, so the header doesn't shift on hover -->
              <template v-if="infoIcon.isOpen">
                <span class="size-48 flex items-center justify-center theme-color-text"><Icon name="info" variant="active" /></span>
              </template>
              <template v-else>
                <span class="size-48 flex items-center justify-center group-enabled:group-hover:hidden"><Icon name="info" /></span>
                <span class="size-48 flex items-center justify-center hidden group-enabled:group-hover:block"><Icon name="info" variant="active" /></span>
              </template>
            </button>

            <router-link
              :to="{ name: 'settings', query: { section: 'profile' } }"
              class="mt-20 group"
              aria-label="Profil">
              <span class="size-48 flex items-center justify-center group-hover:hidden"><Icon name="profile" /></span>
              <span class="size-48 flex items-center justify-center hidden group-hover:block"><Icon name="profile" variant="active" /></span>
            </router-link>
            <button 
              type="button"
              class="w-28 h-24 mt-33 flex items-center justify-center"
              :aria-label="isOpen ? 'Menü schliessen' : 'Menü öffnen'"
              :aria-expanded="isOpen"
              @click="toggle">
              <Icon name="cross" v-if="isOpen" />
              <Icon name="burger" v-else />
            </button>
          </div>
        </div>
      </div>
    </div>
  </header>
  <SidebarMenu :is-open="isOpen" @toggle="toggle" />
</template>
<script setup>
import { useToggleSidebar } from '@/composables/useToggleSidebar';
import { useInfoIconStore } from '@/stores/info';
import { usePageTitle } from '@/composables/usePageTitle';
import IconLogo from '@/components/icons/Logo.vue';
import SidebarMenu from '@/components/menu/Sidebar.vue';
import Icon from '@/components/icons/Icon.vue';
const { isOpen, toggle, hide } = useToggleSidebar();
const infoIcon = useInfoIconStore();

const { title } = usePageTitle();
</script>