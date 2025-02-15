<template>
  <div class="grid grid-cols-12 gap-48 mt-32">
    <div class="col-span-4 flex flex-col gap-y-30">
      <div>
        <h2 class="mb-12 font-muoto-medium">Image Upload</h2>
        <ImageUpload
          :maxSize="250 * 1024 * 1024"
          :allowedTypes="['image/*', 'video/mp4']"
          uploadUrl="/api/upload"
          :multiple="false"
        />
      </div>
      <div>
        <h2 class="mb-12 font-muoto-medium">Image Slideshow</h2>
        <Slideshow :slides="[
          'https://placehold.co/600x400',
          'https://placehold.co/400x400',
          'https://placehold.co/400x200',
          'https://placehold.co/600x900',
          ]" 
        />
      </div>
      <div>
        <h2 class="mb-12 font-muoto-medium">Single Image</h2>
        <ImageCard>
          <Image src="https://placehold.co/600x900" alt="Image" />
        </ImageCard>
      </div>
    </div>
    <div class="col-span-4">
      <h2 class="mb-12 font-muoto-medium">Buttons</h2>
      <div class="flex flex-col gap-y-32">
        <div>
          <h3 class="mb-8 font-muoto-regular">Primary</h3>
          <ButtonGroup>
            <ButtonPrimary label="Abbrechen" type="button" />
            <ButtonPrimary label="Speichern" />
          </ButtonGroup>
        </div>
        <div>
          <h3 class="mb-8 font-muoto-regular">Authentication</h3>
          <ButtonAuth label="Anmelden" />
        </div>
        <div>
          <h3 class="mb-8 font-muoto-regular">Button (box, icon right)</h3>
          <Action 
            label="Erstellen" 
            classes="border pl-8" 
            :icon="{ name: 'Plus', position: 'right' }"
            />
        </div>
        <div>
          <h3 class="mb-8 font-muoto-regular">Button (box, icon left)</h3>
          <Action 
            label="Erstellen"
            classes="border pl-8" 
            :icon="{ name: 'Plus', position: 'left' }" />
        </div>
        <div>
          <h3 class="mb-8 font-muoto-regular">Button (y-only, icon center)</h3>
          <Action 
            label="Benutzer/in" 
            :icon="{ name: 'Plus', position: 'center' }" />
        </div>
        <div>
          <h3 class="mb-8 font-muoto-regular">Button (y-only, icon center, bold)</h3>
          <Action 
            label="Benutzer/in" 
            classes="!font-muoto-medium"
            :icon="{ name: 'Plus', variant: 'small-bold', position: 'center' }" />
        </div>
        <div>
          <h3 class="mb-8 font-muoto-regular">Router Link (y-only, icon right)</h3>
          <Action 
            label="Benutzer/innen"
            :to="{ name: 'Users' }"
            :icon="{ name: 'ChevronRight' }" />
        </div>
        <div>
          <h3 class="mb-8 font-muoto-regular">External Link (y-only, icon right)</h3>
          <Action 
            label="Google"
            href="https://google.com"
            target="_blank" 
            :icon="{ name: 'ChevronRight' }" />
        </div>
        <div>
          <h3 class="mb-8 font-muoto-regular">Button with click event (y-only, icon right)</h3>
          <Action 
            label="Show toast"
            type="button"
            @click="showToast('This is an info message!', 'info')"
            :icon="{ name: 'Plus', position: 'center' }" />
        </div>
        <div>
          <h3 class="mb-8 font-muoto-regular">Toggle Group</h3>
          <ToggleGroup
            v-model="selectedSections"
            name="sections"
            :options="sections" />

          <template v-if="selectedSections.length">
            <div class="bg-snow p-8 mt-12">
              <h3 class="mb-4 font-muoto-regular">Selection</h3>
              {{ selectedSections }}
            </div>
          </template>
        </div>
        <div>
          <h3 class="mb-8 font-muoto-regular">Radio Group</h3>
          <RadioGroup
            v-model="selectedSubscription"
            name="subscription"
            :options="subscriptions"
            class="!border-red-400" />

          <template v-if="selectedSubscription">
            <div class="bg-snow p-8 mt-12">
              <h3 class="mb-4 font-muoto-regular">Selection</h3>
              {{ selectedSubscription }}
            </div>
          </template>
        </div>
      </div>
    </div>
    <div class="col-span-4">
      <div>
        <h2 class="mb-12 font-muoto-medium">Toasts</h2>
        <div class="flex gap-x-8">
          <button 
            @click="showToast('This is an info message!', 'info')"
            class="bg-graphite text-white px-8 py-2 text-sm">
            Show toast 'info' from script
          </button>
          <button 
            @click="toast.show('This is a success message!', 'success')"
            class="bg-graphite text-white px-8 py-2 text-sm">
            Success
          </button>
          <button 
            @click="toast.show('This is an error message!', 'error')"
            class="bg-graphite text-white px-8 py-2 text-sm">
            Error
          </button>
          <button 
            @click="toast.show('This is an info message!', 'info')"
            class="bg-graphite text-white px-8 py-2 text-sm">
            Info
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup>
import { onMounted, ref } from 'vue';
import ImageUpload from '@/components/media/ImageUpload.vue';
import ImageCard from '@/components/media/ImageCard.vue';
import Image from '@/components/media/Image.vue';
import Slideshow from '@/components/media/Slideshow.vue';
import ButtonGroup from '@/components/buttons/Group.vue';
import ButtonPrimary from '@/components/buttons/Primary.vue';
import ButtonAuth from '@/components/buttons/Auth.vue';
import Action from '@/components/buttons/Action.vue';
import ToggleGroup from '@/components/buttons/ToggleGroup.vue';
import RadioGroup from '@/components/buttons/RadioGroup.vue';
import { useToastStore } from '@/store/toast';
const toast = useToastStore();

onMounted(() => {
})

function showToast(message = '', type = 'info') {
  toast.show(message, type)
}

const selectedSections = ref([]);
const sections = [
  { value: 'basic', label: 'Basisinformationen' },
  { value: 'structure', label: 'Struktur' },
  { value: 'template', label: 'Kartenvorlage' },
  { value: 'tags', label: 'Tags' },
  { value: 'users', label: 'Benutzer/innen' },
  { value: 'export', label: 'Export' },
  { value: 'share', label: 'Teilen' }
];

const selectedSubscription = ref('small');
const subscriptions = [
  { value: 'small', label: 'Small' },
  { value: 'medium', label: 'Medium' },
  { value: 'professional', label: 'Professional' }
];


</script>