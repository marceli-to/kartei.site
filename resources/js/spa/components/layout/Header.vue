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
          <h1 class="font-otto-regular text-lg mt-24 ml-8">
            {{ title }}
          </h1>
        </div>
        <div class="w-3/12">
          <div class="flex justify-end gap-x-48">
            <button 
              type="button" 
              class="mt-20 group"
              @click="infoIcon.toggle">
              <template v-if="infoIcon.isActive">
                <IconInfo variant="active" classes="theme-color-text" />
              </template>
              <template v-else>
                <IconInfo classes="group-hover:hidden" />
                <IconInfo variant="active" classes="hidden group-hover:block" />
              </template>
            </button>

            <a href="" class="mt-20 group">
              <IconProfile classes="group-hover:hidden" />
              <IconProfile variant="active" classes="hidden group-hover:block" />
            </a>
            <button 
              class="w-28 h-24 mt-33 flex items-center justify-center"
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
import { useInfoIconStore } from '@/components/infobox/stores/info';
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
defineProps({
  title: {
    type: String,
    default: '',
  },
});
</script>