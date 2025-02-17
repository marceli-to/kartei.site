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
          <Image src="https://placehold.co/600x900" alt="Image" :spacing="'m-40'" />
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
            :icon="{ name: 'Plus', position: 'right' }" />
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
          <h3 class="mb-8 font-muoto-regular">Select Buttons (multiple)</h3>
          <InputSelectButtons
            v-model="selectedSections"
            :multiple="true"
            name="sections"
            wrapperClasses="grid grid-cols-2 gap-8"
            :options="sections" />
          <template v-if="selectedSections.length">
            <div class="bg-snow p-8 mt-12">
              <h3 class="mb-4 font-muoto-regular">Selection</h3>
              {{ selectedSections }}
            </div>
          </template>
        </div>
        <div>
          <h3 class="mb-8 font-muoto-regular">Select Buttons (single)</h3>
          <InputSelectButtons
            v-model="selectedSection"
            name="sections"
            wrapperClasses="flex flex-col gap-y-8"
            :options="sections" />
          <template v-if="selectedSection">
            <div class="bg-snow p-8 mt-12">
              <h3 class="mb-4 font-muoto-regular">Selection</h3>
              {{ selectedSection }}
            </div>
          </template>
        </div>
        <div>
          <h3 class="mb-8 font-muoto-regular">Radio Group</h3>
          <InputRadioGroup
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
      <h2 class="mb-12 font-muoto-medium">Form fields</h2>
      <div class="flex flex-col gap-y-24 w-full">
        <InputGroup>
          <InputLabel label="Bezeichnung / Firma" id="company" required />
          <InputText
            v-model="form.company"
            id="company"
            :error="errors.company"
            @update:error="errors.company = $event"
            :placeholder="errors.company ? errors.company : 'Bezeichnung / Firma'"
            aria-label="Bezeichnung / Firma" />
        </InputGroup>
        <InputGroup>
          <InputLabel label="Adresszusatz" id="address_suffix" />
          <InputText
            v-model="form.address_suffix"
            id="address_suffix"
            :error="errors.address_suffix"
            @update:error="errors.address_suffix = $event"
            :placeholder="errors.address_suffix ? errors.address_suffix : 'Adresszusatz'"
            aria-label="Adresszusatz" />
        </InputGroup>
        <InputGroup>
          <InputLabel label="Hausnummer" id="street_number" />
          <InputText
            v-model="form.street_number"
            id="street_number"
            :error="errors.street_number"
            @update:error="errors.street_number = $event"
            :placeholder="errors.street_number ? errors.street_number : 'Hausnummer'"
            aria-label="Hausnummer" />
        </InputGroup>
        <InputGroup>
          <InputLabel label="PLZ" id="zip" required />
          <InputText
            v-model="form.zip"
            id="zip"
            :error="errors.zip"
            @update:error="errors.zip = $event"
            :placeholder="errors.zip ? errors.zip : 'PLZ'"
            aria-label="PLZ" />
        </InputGroup>
        <InputGroup>
          <InputLabel label="Ort" id="city" required />
          <InputText
            v-model="form.city"
            id="city"
            :error="errors.city"
            @update:error="errors.city = $event"
            :placeholder="errors.city ? errors.city : 'Ort'"
            aria-label="Ort" />
        </InputGroup>
        <InputGroup>
          <InputLabel label="Land" id="country" required />
          <InputSelect
            id="country"
            v-model="form.country"
            :options="countries"
            :error="errors.country"
          />
        </InputGroup>
      </div>

      <div class="flex flex-col gap-y-24 mt-32 w-full">
        <InputGroup>
          <InputLabel label="Passwort" id="password_test" required />
          <div  class="flex flex-col gap-y-8">
            <InputPassword
              id="password_test"
              v-model="form.password"
              :error="errors.password"
              @update:error="errors.password = $event"
              :placeholder="errors.password ? errors.password : 'Passwort'"
              aria-label="Passwort" />
            <InputPassword
              id="password_confirmation"
              v-model="form.password_confirmation"
              :error="errors.password_confirmation"
              @update:error="errors.password_confirmation = $event"
              :placeholder="errors.password_confirmation ? errors.password_confirmation : 'Passwort wiederholen'"
              aria-label="Passwort wiederholen" />
            <ButtonAuth label="Passwort speichern" />
            <div class="text-sm font-muoto-italic text-graphite pl-8">
              Das Passwort muss mindestens 8 Zeichen umfassen sowie Gross- und Kleinbuchstaben, Zahlen und Sonderzeichen enthalten.
            </div>
          </div>
        </InputGroup>
      </div>
      
      <div class="flex flex-col gap-y-24 mt-32 w-full" data-disabled>
        <InputGroup>
          <InputLabel label="Bezeichnung / Firma" id="company_disabled" required />
          <InputText
            v-model="form.company"
            id="company_disabled"
            :error="errors.company"
            @update:error="errors.company = $event"
            :placeholder="errors.company ? errors.company : 'Bezeichnung / Firma'"
            aria-label="Bezeichnung / Firma" />
        </InputGroup>
        <InputGroup>
          <InputLabel label="Adresszusatz" id="address_suffix_disabled" />
          <InputText
            v-model="form.address_suffix"
            id="address_suffix_disabled"
            :error="errors.address_suffix"
            @update:error="errors.address_suffix = $event"
            :placeholder="errors.address_suffix ? errors.address_suffix : 'Adresszusatz'"
            aria-label="Adresszusatz" />
        </InputGroup>
      </div>

      <div class="flex flex-col gap-y-24 mt-48 w-full">
        <h2 class="font-muoto-medium">Form fields auth</h2>
        <div>
          <InputLabel label="Anmelden" class="mb-4 font-muoto-medium" />
          <InputGroup class="flex flex-col gap-y-8">
            <InputText
              id="email"
              v-model="form.email"
              :error="errors.email"
              @update:error="errors.email = $event"
              :placeholder="errors.email ? errors.email : 'E-Mail'"
              aria-label="E-Mail"
              :classes="'bg-white !border-graphite !px-8 font-muoto-medium placeholder:!text-graphite'" />
            <InputText
              id="password"
              type="password"
              v-model="form.password"
              :error="errors.password"
              @update:error="errors.password = $event"
              :placeholder="errors.password ? errors.password : 'Passwort'"
              aria-label="Passwort"
              :classes="'bg-white !border-graphite !px-8 font-muoto-medium placeholder:!text-graphite'" />
              <ButtonAuth label="Anmelden" />
          </InputGroup>
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
import InputSelectButtons from '@/components/fields/SelectButtons.vue';
import InputRadioGroup from '@/components/fields/RadioGroup.vue';
import InputGroup from '@/components/fields/Group.vue';
import InputLabel from '@/components/fields/Label.vue';
import InputText from '@/components/fields/Text.vue';
import InputPassword from '@/components/fields/Password.vue';
import InputSelect from '@/components/fields/Select.vue';

import { useToastStore } from '@/store/toast';
const toast = useToastStore();

onMounted(() => {
})

function showToast(message = '', type = 'info') {
  toast.show(message, type)
}

const form = ref({
  email: '',
  password: '',
  password_confirmation: '',
  firstname: '',
  company: '',
  address_suffix: '',
  street_number: '',
  zip: '',
  city: '',
  country: 'switzerland'
});

const errors = ref({
  email: null,
  password: null,
  password_confirmation: null,
  firstname: 'Vorname fehlt',
  company: null,
  address_suffix: null,
  street_number: null,
  zip: null,
  city: 'Ort fehlt',
  country: null
});

const countries = [
  { value: 'switzerland', label: 'Schweiz' },
  { value: 'germany', label: 'Deutschland' },
  { value: 'austria', label: 'Österreich' }
];

const selectedSection = ref('basic');
const selectedSections = ref(['basic', 'structure']);
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