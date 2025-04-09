<template>
  <Slide>
    <form class="w-full h-full flex flex-col justify-between pb-24 relative">

      <!-- Content -->
      <div class="flex flex-col gap-y-20">

        <div>
          <ImageCard class="flex items-center justify-center mb-8">
            <IconImage v-if="form.image" />
            <IconImage variant="missing" v-else />
          </ImageCard>
          <InputGroup>
            <InputSelectButtons
              v-model="form.image"
              name="image"
              wrapperClasses="flex flex-col gap-y-8"
              :options="image_options" />
          </InputGroup>
        </div>

        <InputGroup>
          <InputLabel label="Nr. / ID" />
          <InputStatic class="font-muoto-medium">
            Nr. / ID (wird automatisch generiert)
          </InputStatic>
        </InputGroup>
      </div>

      <!-- Buttons -->
      <ButtonGroup>
        <ButtonPrimary type="submit" label="Speichern" :disabled="isSaving" />
      </ButtonGroup>
    </form>
  </Slide>
</template>
<script setup>
import { ref } from 'vue';
import { useRoute } from 'vue-router';
import { useToastStore } from '@/components/toast/stores/toast';
import Slide from '@/components/slider/Slide.vue';
import ButtonGroup from '@/components/buttons/Group.vue';
import ButtonPrimary from '@/components/buttons/Primary.vue';
import InputLabel from '@/components/forms/Label.vue';
import InputText from '@/components/forms/Text.vue';
import InputStatic from '@/components/forms/Static.vue';
import InputSelectButtons from '@/components/forms/SelectButtons.vue';
import InputGroup from '@/components/forms/Group.vue';
import ImageCard from '@/components/media/Card.vue';
import IconImage from '@/components/icons/Image.vue';

const toast = useToastStore();
const route = useRoute();
const uuid = ref(route.params.uuid || null);
const isSaving = ref(false);

const form = ref({
  image: true
});

const image_options = [
  { value: true, label: 'Bildfeld' },
  { value: false, label: 'Ohne Bildfeld' }
];

const props = defineProps({
  isActive: {
    type: Boolean,
    default: false
  }
});

</script>
