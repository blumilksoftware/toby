<script setup>
import { Dialog, DialogOverlay, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'
import { useForm } from '@inertiajs/inertia-vue3'
import { computed, ref, watch } from 'vue'

const props = defineProps({
  show: {
    type: Boolean,
    default: false,
  },
  limit: Object,
  year: Number,
})

const emit = defineEmits(['close', 'changed'])

const allDays = ref(0)

const form = useForm({
  days: 0,
})

const remaining = computed(() => allDays.value - form.days)

function cancel() {
  emit('close')
}

function submitForm() {
  form
    .transform(data => ({
      ...data,
      city: data.city?.id,
    }))
    .post(`/vacation/limits/${props.limit.id}/take-from-last-year`, {
      preserveState: (page) => Object.keys(page.props.errors).length,
      preserveScroll: true,
      onSuccess() {
        emit('changed')
      },
    })
}

watch(() => props.show, () => {
  if (!props.show || !props.limit) {
    return
  }

  allDays.value = props.limit.remainingLastYear + props.limit.fromPreviousYear
  form.defaults('days', props.limit.fromPreviousYear)
  form.reset()
  form.clearErrors()
})
</script>

<template>
  <TransitionRoot
    as="template"
    :show="show"
  >
    <Dialog
      is="div"
      class="overflow-y-auto fixed inset-0 z-10"
      @close="cancel()"
    >
      <div class="flex justify-center items-end px-4 pt-4 pb-20 min-h-screen text-center sm:block sm:p-0">
        <TransitionChild
          as="template"
          enter="ease-out duration-300"
          enter-from="opacity-0"
          enter-to="opacity-100"
          leave="ease-in duration-200"
          leave-from="opacity-100"
          leave-to="opacity-0"
        >
          <DialogOverlay class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" />
        </TransitionChild>

        <span class="hidden sm:inline-block sm:h-screen sm:align-middle">&#8203;</span>
        <TransitionChild
          as="template"
          enter="ease-out duration-300"
          enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
          enter-to="opacity-100 translate-y-0 sm:scale-100"
          leave="ease-in duration-200"
          leave-from="opacity-100 translate-y-0 sm:scale-100"
          leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        >
          <form
            class="inline-block relative px-4 pt-5 pb-4 text-left align-bottom bg-white rounded-lg shadow-xl transition-all transform sm:p-6 sm:my-8 sm:w-full sm:max-w-md sm:align-middle"
            @submit.prevent="submitForm"
          >
            <div>
              <DialogTitle
                as="h3"
                class="text-lg font-medium leading-6 text-center text-gray-900 font-sembiold"
              >
                Przenieś dni
              </DialogTitle>
              <div class="mt-5 space-y-4">
                <div>
                  <span class="block text-sm font-medium text-gray-700 sm:mt-px mb-2">
                    Pracownik
                  </span>
                  <div class="mt-1 sm:col-span-2 sm:mt-0">
                    <div class="flex justify-start items-center">
                      <span class="inline-flex justify-center items-center w-10 h-10 rounded-full">
                        <img
                          class="w-10 h-10 rounded-full"
                          :src="limit.user.avatar"
                        >
                      </span>
                      <div class="ml-3">
                        <div class="text-sm font-medium text-gray-900">
                          {{ limit.user.name }}
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div>
                  <label
                    for="name"
                    class="block text-sm font-medium text-gray-700 mb-2"
                  >
                    Przeniesione dni na rok {{ year + 1 }}
                  </label>
                  <input
                    v-model="form.days"
                    type="number"
                    min="0"
                    class="block w-full disabled:text-slate-500 disabled:bg-slate-50 rounded-md sm:text-sm"
                    :class="{ 'border-red-300 text-red-900 focus:outline-none focus:ring-red-500 focus:border-red-500': form.errors.days, 'focus:ring-blumilk-500 focus:border-blumilk-500 sm:text-sm border-gray-300': !form.errors.days }"
                  >
                  <p
                    v-if="form.errors.days"
                    class="mt-2 text-sm text-red-600"
                  >
                    {{ form.errors.days }}
                  </p>
                </div>
                <div>
                  <label
                    for="name"
                    class="block text-sm font-medium text-gray-700 mb-2"
                  >
                    Pozostałe dni w roku {{ year }}
                  </label>
                  <div class="inline-flex items-center py-2 px-4 mt-1 w-full max-w-lg text-gray-500 bg-gray-50 rounded-md border border-gray-300 sm:col-span-2 sm:mt-0 sm:text-sm">
                    {{ remaining }}
                  </div>
                </div>
              </div>
            </div>
            <div class="mt-5 sm:mt-6">
              <div class="flex justify-end space-x-3">
                <button
                  type="button"
                  class="py-2 px-4 text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blumilk-500 focus:ring-offset-2 shadow-sm"
                  @click="cancel()"
                >
                  Anuluj
                </button>
                <button
                  type="submit"
                  :disabled="form.processing"
                  class="inline-flex justify-center py-2 px-4 text-base font-medium text-white bg-blumilk-600 hover:bg-blumilk-700 rounded-md border border-transparent focus:outline-none focus:ring-2 focus:ring-blumilk-500 focus:ring-offset-2 shadow-sm sm:text-sm"
                >
                  Zapisz zmiany
                </button>
              </div>
            </div>
          </form>
        </TransitionChild>
      </div>
    </Dialog>
  </TransitionRoot>
</template>
