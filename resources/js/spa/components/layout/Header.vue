<template>
  <header class="relative z-90 dark:bg-black">
    <div class="flex min-h-80 border-b border-b-black dark:border-b-white">
      <div class="flex w-full leading-none">
        <div class="w-2/12 min-w-nav">
          <router-link
            :to="{ name: 'home' }"
            class="mt-25 block"
            @click="hide">
            <IconLogo class="text-black dark:text-white" />
          </router-link>
        </div>
        <div class="w-7/12">
          <h1 class="ml-8 mt-30 font-otto-regular text-title">
            {{ title }}
          </h1>
        </div>
        <div class="w-3/12">
          <div class="flex justify-end gap-x-48">
            <button
              type="button"
              class="group mt-20 disabled:cursor-default"
              :disabled="!infoIcon.isAvailable"
              :aria-pressed="infoIcon.isOpen"
              aria-label="Info"
              @click="infoIcon.toggle">
              <!-- Same 48px box for both variants, so the header doesn't shift on hover -->
              <template v-if="infoIcon.isOpen">
                <span class="theme-color-text flex size-48 items-center justify-center"
                  ><Icon
                    name="info"
                    variant="active"
                /></span>
              </template>
              <template v-else>
                <span class="flex size-48 items-center justify-center group-enabled:group-hover:hidden"
                  ><Icon name="info"
                /></span>
                <span class="flex hidden size-48 items-center justify-center group-enabled:group-hover:block"
                  ><Icon
                    name="info"
                    variant="active"
                /></span>
              </template>
            </button>

            <router-link
              :to="{ name: 'settings', query: { section: 'profile' } }"
              class="group mt-20"
              aria-label="Profil">
              <span class="flex size-48 items-center justify-center group-hover:hidden"><Icon name="profile" /></span>
              <span class="flex hidden size-48 items-center justify-center group-hover:block"
                ><Icon
                  name="profile"
                  variant="active"
              /></span>
            </router-link>
            <button
              type="button"
              class="mt-33 flex h-24 w-28 items-center justify-center"
              :aria-label="isOpen ? 'Menü schliessen' : 'Menü öffnen'"
              :aria-expanded="isOpen"
              @click="toggle">
              <Icon
                v-if="isOpen"
                name="cross" />
              <Icon
                v-else
                name="burger" />
            </button>
          </div>
        </div>
      </div>
    </div>
  </header>
  <SidebarMenu
    :is-open="isOpen"
    @toggle="toggle" />
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
