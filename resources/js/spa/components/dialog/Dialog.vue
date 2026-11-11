<template>
  <Teleport to="body">
    <Transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="transform opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition duration-200 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="transform opacity-0">
      <div
        v-if="isVisible"
        class="fixed inset-0 z-9999 overflow-y-auto"
        @mousedown="onPointerDown"
        @click="onBackdropClick">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black/80 transition-opacity duration-200"></div>
        <!-- Dialog -->
        <div class="relative flex min-h-full items-center justify-center p-4">
          <div
            ref="panel"
            tabindex="-1"
            :class="[
              'relative w-full transform overflow-hidden bg-white p-24 text-left shadow-xl transition-all outline-none dark:text-black',
              getSizeClass(content.size),
            ]"
            role="dialog"
            aria-modal="true"
            :aria-labelledby="content.title ? 'dialog-title' : null"
            :aria-describedby="content.message ? 'dialog-desc' : null"
            @keydown.tab="trapFocus">
            <button
              v-if="!content.hideClose"
              type="button"
              class="absolute top-24 right-24"
              aria-label="Schliessen"
              @click="handleCancel">
              <Icon
                name="cross"
                variant="small-bold" />
            </button>
            <!-- flex-1 down to the content: lets a "full" dialog's component fill the height -->
            <div class="mx-auto flex flex-1 flex-col gap-y-24">
              <!-- Title -->
              <h3
                v-if="content.title"
                id="dialog-title"
                class="font-muoto-medium">
                {{ content.title }}
              </h3>

              <!-- Content -->
              <div class="flex flex-1 flex-col">
                <!-- Simple message -->
                <p
                  v-if="content.message"
                  id="dialog-desc">
                  {{ content.message }}
                </p>
                <!-- ! Simple message -->

                <!-- Dynamic component -->
                <component
                  :is="content.component"
                  v-else-if="content.component"
                  v-bind="content.props || {}" />
                <!-- ! Dynamic component -->

                <!-- Default slot -->
                <slot></slot>
                <!-- ! Default slot -->
              </div>
              <!-- ! Content -->

              <!-- Actions -->
              <div v-if="!content.hideDefaultActions">
                <slot name="actions">
                  <ButtonGroup>
                    <AppButton
                      v-if="content.onConfirm"
                      :label="content.confirmLabel || 'Speichern'"
                      type="button"
                      :disabled="isBusy"
                      @click="handleConfirm" />
                    <AppButton
                      v-if="content.onCancel"
                      :label="content.cancelLabel || 'Abbrechen'"
                      type="button"
                      :disabled="isBusy"
                      @click="handleCancel" />
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
import { nextTick, onMounted, onUnmounted, ref, watch } from 'vue';
import { storeToRefs } from 'pinia';
import { useDialogStore } from '@/stores/dialog';
import ButtonGroup from '@/components/buttons/Group.vue';
import Icon from '@/components/icons/Icon.vue';
import AppButton from '@/components/buttons/Button.vue';

const dialogStore = useDialogStore();
const { isVisible, isBusy, content, version } = storeToRefs(dialogStore);
const { hide } = dialogStore;

onMounted(() => {
  const handleKey = (e) => {
    if (e.key === 'Escape' && isVisible.value) {
      handleBackdropClick();
    }
  };
  window.addEventListener('keydown', handleKey);
  onUnmounted(() => window.removeEventListener('keydown', handleKey));
});

const panel = ref(null);

// Focus moves into the dialog when it opens and back to where it was
// (usually the button that opened it) when it closes
let returnFocusTo = null;
watch(isVisible, async (visible) => {
  if (visible) {
    returnFocusTo = document.activeElement;
    await nextTick();
    panel.value?.focus();
  } else if (returnFocusTo?.isConnected) {
    returnFocusTo.focus();
    returnFocusTo = null;
  }
});

const FOCUSABLE =
  'a[href], button:not([disabled]), input:not([disabled]):not([type="hidden"]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])';

// Tab and Shift+Tab cycle inside the dialog
const trapFocus = (event) => {
  const items = [...panel.value.querySelectorAll(FOCUSABLE)].filter((el) => el.offsetParent !== null);
  if (!items.length) {
    event.preventDefault();
    return;
  }
  const first = items[0];
  const last = items[items.length - 1];
  const active = document.activeElement;
  if (event.shiftKey && (active === first || active === panel.value)) {
    event.preventDefault();
    last.focus();
  } else if (!event.shiftKey && active === last) {
    event.preventDefault();
    first.focus();
  }
};

// Only a click that starts and ends outside the panel closes the dialog;
// selecting text inside and releasing the mouse outside must not
let pressedInside = false;
const onPointerDown = (event) => {
  pressedInside = !!panel.value?.contains(event.target);
};
const onBackdropClick = (event) => {
  if (pressedInside || panel.value?.contains(event.target)) return;
  handleBackdropClick();
};

const getSizeClass = (size) => {
  switch (size) {
    case 'small':
      return 'max-w-sm';
    case 'medium':
      return 'max-w-md';
    case 'large':
      return 'max-w-4xl';
    case 'xlarge':
      return 'max-w-5xl';
    // Full height minus 40px above and below (record delete dialog, KAR0001_Desktop_261111 p. 11)
    case 'full':
      return 'max-w-[956px] min-h-[calc(100vh-80px)] flex flex-col';
    default:
      return 'max-w-2xl';
  }
};

// onConfirm may be async: keep the dialog open (and busy) until it settles.
// Returning false keeps it open; a dialog opened by onConfirm itself is left alone.
const handleConfirm = async () => {
  if (isBusy.value) return;
  const current = version.value;
  let result;
  isBusy.value = true;
  try {
    result = await content.value.onConfirm?.();
  } finally {
    if (version.value === current) {
      isBusy.value = false;
      if (result !== false) hide();
    }
  }
};

const handleCancel = () => {
  if (isBusy.value) return;
  if (content.value.onCancel) {
    content.value.onCancel();
  }
  hide();
};

const handleBackdropClick = () => {
  if (isBusy.value) return;
  if (content.value.onCancel) {
    handleCancel();
  } else {
    hide();
  }
};
</script>
