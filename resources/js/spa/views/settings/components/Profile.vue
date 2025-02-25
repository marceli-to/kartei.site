<template>
  <form 
    @submit.prevent="handleSubmit"
    class="px-8 relative w-full flex flex-col justify-between pb-16 h-full -top-24"
    v-if="!isLoading">
    <div class="flex flex-col gap-y-20">
      <InputGroup>
        <InputLabel label="Vorname" id="firstname" required />
        <InputText
          v-model="form.firstname"
          id="firstname"
          :error="errors.firstname"
          @update:error="errors.firstname = $event"
          :placeholder="errors.firstname ? errors.firstname : 'Vorname'"
          aria-label="Vorname" />
      </InputGroup>
      <InputGroup>
        <InputLabel label="Name" id="name" required />
        <InputText
          v-model="form.name"
          id="name"
          :error="errors.name"
          @update:error="errors.name = $event"
          :placeholder="errors.name ? errors.name : 'Nachname'"
          aria-label="Nachname" />
      </InputGroup>
      <InputGroup>
        <InputLabel label="E-Mail-Adresse" id="email" required />
        <InputText
          v-model="form.email"
          id="email"
          :error="errors.email"
          @update:error="errors.email = $event"
          :placeholder="errors.email ? errors.name : 'E-Mail-Adresse'"
          aria-label="E-Mail-Adresse" />
      </InputGroup>
    </div>
    <div>
      <ButtonGroup>
        <ButtonPrimary label="Abbrechen" type="button" />
        <ButtonPrimary label="Speichern" />
      </ButtonGroup>
    </div>
  </form>
</template>
<script setup>
import { ref, onMounted } from 'vue';
import { getUser, updateUser } from '@/services/api/user';
import InputGroup from '@/components/fields/Group.vue';
import InputLabel from '@/components/fields/Label.vue';
import InputText from '@/components/fields/Text.vue';
import ButtonGroup from '@/components/buttons/Group.vue';
import ButtonPrimary from '@/components/buttons/Primary.vue';
import { useToastStore } from '@/store/toast';
const toast = useToastStore();

const isLoading = ref(false);

const form = ref({
  uuid: null,
  firstname: '',
  name: '',
  email: '',
});

const errors = ref({
  firstname: null,
  name: null,
  email: null,
});

onMounted(async () => {
  try {
    isLoading.value = true;
    const userData = await getUser();
    if (userData.user) {
      form.value.uuid = userData.user.uuid;
      form.value.firstname = userData.user.firstname;
      form.value.name = userData.user.name;
      form.value.email = userData.user.email;
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
    isLoading.value = true;
    const userData = await updateUser(form.value);
    if (userData.email_changed) {
      toast.show('E-Mail-Adresse wurde geändert, wir haben einen neuen Verifizierungslink geschickt.', 'success');
    }
  } 
  catch (error) {
    errors.value = {
      firstname: error.response?.data?.errors?.firstname?.[0],
      name: error.response?.data?.errors?.name?.[0],
      email: error.response?.data?.errors?.email?.[0],
    };
  }
  finally {
    isLoading.value = false;
  }
};

</script>