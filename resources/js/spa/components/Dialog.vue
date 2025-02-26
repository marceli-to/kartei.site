<template>
  <Teleport to="body">
    <Transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="transform opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition duration-200 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="transform opacity-0"
    >
      <div
        v-if="isVisible"
        class="fixed inset-0 z-[9999] overflow-y-auto"
        @click="handleBackdropClick"
      >
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black/80"></div>

        <!-- Dialog -->
        <div class="flex min-h-full items-center justify-center p-4 relative">
          <div
            class="relative w-full max-w-3xl transform overflow-hidden bg-white p-24 text-left shadow-xl transition-all"
            @click.stop>

            <a 
              href="javascript:;" 
              class="absolute right-24 top-24"
              @click="isVisible = false">
              <IconCross variant="small-bold" />
            </a>

            <div class="flex flex-col gap-y-24 max-w-lg mx-auto">

              <!-- Title -->
              <h3 v-if="content.title" class="font-muoto-medium">
                {{ content.title }}
              </h3>

              <!-- Content -->
              <div>

                <!-- Simple message -->
                <p v-if="content.message">
                  {{ content.message }}
                </p>
                <!-- ! Simple message -->
                
                <!-- Dynamic component -->
                <component
                  v-else-if="content.component"
                  :is="content.component"
                  v-bind="content.props || {}"
                />
                <!-- ! Dynamic component -->
                
                <!-- Default slot -->
                <slot></slot>
                <!-- ! Default slot -->

              </div>
              <!-- ! Content -->

              <!-- Actions -->
              <div>
                <slot name="actions">
                  <ButtonGroup>
                    <ButtonPrimary 
                      :label="content.cancelLabel || 'Abbrechen'" 
                      type="button"
                      @click="handleCancel"
                      v-if="content.onCancel" />
                    <ButtonPrimary 
                      :label="content.confirmLabel || 'Speichern'"
                      type="button"
                      @click="handleConfirm"
                      v-if="content.onConfirm" />
                  </ButtonGroup>
                </slot>
              </div>
              <!-- ! Actions -->
            </div>
            
          </div>

        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { storeToRefs } from 'pinia';
import { useDialogStore } from '@/stores/dialog';
import ButtonGroup from '@/components/buttons/Group.vue';
import ButtonPrimary from '@/components/buttons/Primary.vue';
import IconCross from '@/components/icons/Cross.vue';

const dialogStore = useDialogStore();
const { isVisible, content } = storeToRefs(dialogStore);
const { hide } = dialogStore;

const handleConfirm = () => {
  if (content.value.onConfirm) {
    content.value.onConfirm();
  }
  hide();
};

const handleCancel = () => {
  if (content.value.onCancel) {
    content.value.onCancel();
  }
  hide();
};

const handleBackdropClick = () => {
  if (content.value.onCancel) {
    handleCancel();
  } else {
    hide();
  }
};
</script>