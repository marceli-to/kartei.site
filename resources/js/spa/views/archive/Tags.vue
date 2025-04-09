<template>
  <Slide class="pb-40">
    <form 
      @submit.prevent="handleSubmit"
      class="w-full h-full flex flex-col justify-between"
      v-if="!isLoading">

      <!-- Content -->
      <div class="flex flex-col gap-y-32">
        <div>
          <Action 
            type="button"
            label="Tag" 
            :icon="{ name: 'Plus', variant: 'small-bold', position: 'center' }"
            @click="add" />
        </div>
        <div class="flex flex-col gap-y-8">
          <InputGroup
            class="relative"
            v-for="(tag, index) in form.tags" :key="index">
            <InputText
              v-model="form.tags[index].name"
              :placeholder="`Tag ${index + 1}`"
              aria-label="Tag"
              :ref="el => inputRefs[index] = el" />
              <button 
                type="button" 
                class="absolute right-8 top-1/2 -translate-y-1/2"
                @click="remove(index)">
                <IconCross variant="small" />
              </button>
          </InputGroup>
        </div>
      </div>

      <!-- Actions -->
      <div v-if="isActive">
        <ButtonGroup>
          <ButtonPrimary label="Speichern" :disabled="isSaving" />
        </ButtonGroup>
      </div>
    </form>
  </Slide>
</template>

<script setup>
import { ref, onMounted, nextTick } from 'vue';
import { useRoute } from 'vue-router';
import { storeTags, getTags, deleteTag } from '@/services/api/tags';
import { useToastStore } from '@/components/toast/stores/toast';
import Slide from '@/components/slider/Slide.vue';
import InputGroup from '@/components/forms/Group.vue';
import InputText from '@/components/forms/Text.vue';
import ButtonGroup from '@/components/buttons/Group.vue';
import ButtonPrimary from '@/components/buttons/Primary.vue';
import Action from '@/components/buttons/Action.vue';
import IconCross from '@/components/icons/Cross.vue';

const toast = useToastStore();

const props = defineProps({
  isActive: {
    type: Boolean,
    default: false
  }
});

const route = useRoute();
const uuid = ref(route.params.uuid || null);

const isSaving = ref(false);
const isLoading = ref(false);
const inputRefs = ref([]);

const form = ref({
  tags: [{
    uuid: null,
    name: ''
  }]
});

onMounted(async () => {
  try {
    isLoading.value = true;
    if (uuid.value) {
      await fetchTags();
    }
  } 
  catch (error) {
    console.error(error);
  }
  finally {
    isLoading.value = false;
  }
});

const handleSubmit = async () => {
  try {
    isSaving.value = true;
    const validTags = form.value.tags.filter(tag => tag.name.trim() !== '');
    const response = await storeTags(uuid.value, validTags);
    if (response?.data) {
      form.value.tags = response.data;
    }
    toast.show('Tag/s erfolgreich gespeichert.', 'success');
  } 
  catch (error) { 
    console.error(error);
    toast.show('Fehler beim Speichern der Tags.', 'error');
  }
  finally {
    isSaving.value = false;
  }
};

const fetchTags = async () => {
  if (!uuid.value) return;
  
  try {
    const response = await getTags(uuid.value);
    if (response?.data) {
      form.value.tags = response.data;
    }
  } 
  catch (error) {
    
  }
};

const add = async () => {
  form.value.tags.push({
    uuid: null,
    name: ''
  });
  await nextTick();
  const lastIndex = form.value.tags.length - 1;
  inputRefs.value[lastIndex]?.$el?.querySelector('input')?.focus();
}

const remove = async (index) => {
  const tagUuid = form.value.tags[index].uuid;
  if (tagUuid) {
    try {
      await deleteTag(tagUuid);
    } catch (error) {
      console.error(error);
      toast.show('Fehler beim Löschen des Tags.', 'error');
    }
  }
  form.value.tags.splice(index, 1);
  if (form.value.tags.length === 0) {
    form.value.tags.push({ uuid: null, name: '' });
  }
};

</script>
