<template>
  <header class="relative z-90">
    <div class="min-h-80 flex border-b border-b-black dark:border-b-white">
      <div class="w-full flex justify-between leading-none">
        <router-link 
          :to="{ name: 'home' }" 
          class="mt-25" 
          @click="sidebar.hide">
          <IconLogo class="text-black dark:text-white" />
        </router-link>
        <div class="flex justify-end gap-x-48">
          <a 
            href="javascript:;" 
            class="mt-20 group"
            @click="infoIcon.toggle">
            <template v-if="infoIcon.isActive">
              <IconInfo variant="active" classes="theme-color-text" />
            </template>
            <template v-else>
              <IconInfo classes="group-hover:hidden" />
              <IconInfo variant="active" classes="hidden group-hover:block" />
            </template>
          </a>

          <a href="" class="mt-20 group">
            <IconProfile classes="group-hover:hidden" />
            <IconProfile variant="active" classes="hidden group-hover:block" />
          </a>
          <button 
            class="w-28 h-24 mt-33 flex items-center justify-center"
            @click="sidebar.toggle">
            <IconCross v-if="sidebar.isOpen.value" />
            <IconBurger v-else />
          </button>
        </div>
      </div>
    </div>
  </header>
  <SidebarMenu :is-open="sidebar.isOpen.value" @toggle="sidebar.toggle" />
</template>
<script setup>
import IconLogo from '@/components/icons/Logo.vue';
import IconInfo from '@/components/icons/Info.vue';
import IconProfile from '@/components/icons/Profile.vue';
import IconBurger from '@/components/icons/Burger.vue';
import IconCross from '@/components/icons/Cross.vue';
import SidebarMenu from '@/components/menu/Sidebar.vue';

import { useToggleSidebar } from '@/composables/useToggleSidebar';
const sidebar = useToggleSidebar();

import { useInfoIconStore } from '@/components/infobox/stores/info';
const infoIcon = useInfoIconStore();

</script>