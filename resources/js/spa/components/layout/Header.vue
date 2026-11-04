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
              <template v-if="infoIcon.isOpen">
                <IconInfo variant="active" classes="theme-color-text" />
              </template>
              <template v-else>
                <IconInfo classes="group-enabled:group-hover:hidden" />
                <IconInfo variant="active" classes="hidden group-enabled:group-hover:block" />
              </template>
            </button>

            <router-link
              :to="{ name: 'settings', query: { section: 'profile' } }"
              class="mt-20 group"
              aria-label="Profil">
              <IconProfile classes="group-hover:hidden" />
              <IconProfile variant="active" classes="hidden group-hover:block" />
            </router-link>
            <button 
              type="button"
              class="w-28 h-24 mt-33 flex items-center justify-center"
              :aria-label="isOpen ? 'Menü schliessen' : 'Menü öffnen'"
              :aria-expanded="isOpen"
              @click="toggle">
              <IconCross v-if="isOpen" />
              <IconBurger v-else />
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
import IconInfo from '@/components/icons/Info.vue';
import IconProfile from '@/components/icons/Profile.vue';
import IconBurger from '@/components/icons/Burger.vue';
import IconCross from '@/components/icons/Cross.vue';
import SidebarMenu from '@/components/menu/Sidebar.vue';
const { isOpen, toggle, hide } = useToggleSidebar();
const infoIcon = useInfoIconStore();

const { title } = usePageTitle();
</script>