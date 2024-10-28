<script setup>
import { useForm } from '@inertiajs/vue3'
import FlatPickr from 'vue-flatpickr-component'
import { CheckIcon, ChevronUpDownIcon, XCircleIcon } from '@heroicons/vue/24/solid'
import { reactive, ref, watch } from 'vue'
import { Listbox, ListboxButton, ListboxLabel, ListboxOption, ListboxOptions } from '@headlessui/vue'
import SettlementType from '@/Shared/SettlementType.vue'
import axios from 'axios'
import InertiaLink from '@/Shared/InertiaLink.vue'
import { useGlobalProps } from '@/Composables/useGlobalProps'
import AppLayout from '@/Shared/Layout/AppLayout.vue'

const props = defineProps({
  overtimeFromDate: [String, null],
  settlementTypes: Array,
})

const { auth } = useGlobalProps()

const form = useForm({
  user: auth.value.user,
  from: props.overtimeFromDate,
  to: props.overtimeFromDate,
  settlementType: props.settlementTypes[0],
  comment: null,
})

let isDirty = ref(false)

watch(form, formData => {
  const { from, to } = formData.data()
  isDirty.value = formData.isDirty || from !== null || to !== null
}, { immediate: true, deep: true })

refreshEstimatedHours(form.from, form.to)

const estimatedHours = ref(0)

const fromInputConfig = reactive({
  enableTime: true,
  dateFormat: 'Y-m-d H:i',
  altFormat: 'd.m.Y H:i',
})

const toInputConfig = reactive({
  enableTime: true,
  dateFormat: 'Y-m-d H:i',
  altFormat: 'd.m.Y H:i',
})

function createForm() {
  form
    .transform(data => ({
      ...data,
      user: data.user.id,
      type: data.settlementType.value,
    }))
    .post('/overtime/requests')
}

function onFromChange(selectedDates, dateStr) {
  if (form.to === null) {
    form.to = dateStr


  }
}

function onToChange(selectedDates, dateStr) {
  if (form.from === null) {
    form.from = dateStr
  }
}

watch(() => form.to, () => {
  refreshEstimatedHours(form.from, form.to)
})

watch(() => form.from, () => {
  refreshEstimatedHours(form.from, form.to)
})
async function refreshEstimatedHours(from, to) {
  if (from && to) {
    const res = await axios.post(
      '/api/overtime/calculate-hours',
      { from: from, to: to },
    )

    estimatedHours.value = res.data
  }
}
</script>

<template>
  <AppLayout title="Dodaj nadgodziny">
    <div class="mx-auto w-full max-w-7xl">
      <div class="flex flex-col h-full bg-white shadow-md xl:col-span-2">
        <div class="p-4 sm:px-6">
          <h2 class="text-lg font-medium leading-6 text-gray-900">
            Dodaj wniosek
          </h2>
        </div>
        <form
          class="px-6 h-full border-t border-gray-200"
          @submit.prevent="createForm"
        >
          <div class="flex flex-col justify-around h-full">
            <div>
              <div
                v-if="form.errors.overtimeRequest"
                class="p-4 mt-2 bg-red-50 rounded-md"
              >
                <div class="flex">
                  <div class="shrink-0">
                    <XCircleIcon class="size-5 text-red-400" />
                  </div>
                  <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">
                      Wniosek nie mógł zostać utworzony
                    </h3>
                    <div class="mt-2 text-sm text-red-700">
                      <span>{{ form.errors.overtimeRequest }}</span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="items-center py-4 sm:grid sm:grid-cols-3">
                <label
                  for="date_from"
                  class="block text-sm font-medium text-gray-700 sm:mt-px"
                >
                  Osoba składająca wniosek
                </label>
                <div class="mt-1 sm:col-span-2 sm:mt-0">
                  <div class="flex justify-start items-center">
                    <span class="inline-flex justify-center items-center size-10 rounded-full">
                      <img
                        class="size-10 rounded-full"
                        :src="auth.user.avatar"
                      >
                    </span>
                    <div class="ml-3">
                      <div class="text-sm font-medium text-gray-900">
                        {{ auth.user.name }}
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="items-center py-4 sm:grid sm:grid-cols-3">
                <label
                  for="date_from"
                  class="block text-sm font-medium text-gray-700 sm:mt-px"
                >
                  Data od
                </label>
                <div class="mt-1 sm:col-span-2 sm:mt-0">
                  <FlatPickr
                    id="date_from"
                    v-model="form.from"
                    :config="fromInputConfig"
                    class="block w-full max-w-lg rounded-md shadow-sm sm:text-sm"
                    :class="{ 'border-red-300 text-red-900 focus:outline-none focus:ring-red-500 focus:border-red-500': form.errors.from, 'focus:ring-blumilk-500 focus:border-blumilk-500 sm:text-sm border-gray-300': !form.errors.from }"
                    @on-change="onFromChange"
                  />
                  <p
                    v-if="form.errors.from"
                    class="mt-2 text-sm text-red-600"
                  >
                    {{ form.errors.from }}
                  </p>
                </div>
              </div>
              <div class="items-center py-4 sm:grid sm:grid-cols-3">
                <label
                  for="date_from"
                  class="block text-sm font-medium text-gray-700 sm:mt-px"
                >
                  Data do
                </label>
                <div class="mt-1 sm:col-span-2 sm:mt-0">
                  <FlatPickr
                    id="date_to"
                    v-model="form.to"
                    :config="toInputConfig"
                    class="block w-full max-w-lg rounded-md shadow-sm sm:text-sm"
                    :class="{ 'border-red-300 text-red-900 focus:outline-none focus:ring-red-500 focus:border-red-500': form.errors.to, 'focus:ring-blumilk-500 focus:border-blumilk-500 sm:text-sm border-gray-300': !form.errors.to }"
                    @on-change="onToChange"
                  />
                  <p
                    v-if="form.errors.to"
                    class="mt-2 text-sm text-red-600"
                  >
                    {{ form.errors.to }}
                  </p>
                </div>
              </div>
              <div class="items-center py-4 sm:grid sm:grid-cols-3">
                <span class="block text-sm font-medium text-gray-700 sm:mt-px">Liczba godzin</span>
                <div
                  class="inline-flex items-center py-2 px-4 mt-1 w-full max-w-lg text-gray-500 bg-gray-50 rounded-md border border-gray-300 sm:col-span-2 sm:mt-0 sm:text-sm"
                >
                  {{ estimatedHours }}
                </div>
              </div>
              <Listbox
                v-model="form.settlementType"
                as="div"
                class="items-center py-4 sm:grid sm:grid-cols-3"
              >
                <ListboxLabel class="block text-sm font-medium text-gray-700">
                  Typ rozliczenia nadgodzin
                </ListboxLabel>
                <div class="relative mt-1 sm:col-span-2 sm:mt-0">
                  <ListboxButton
                    class="relative py-2 pr-10 pl-3 w-full max-w-lg text-left bg-white rounded-md border focus:outline-none focus:ring-1 shadow-sm cursor-default sm:text-sm"
                    :class="{ 'border-red-300 text-red-900 focus:ring-red-500 focus:border-red-500': form.errors.type, 'focus:ring-blumilk-500 focus:border-blumilk-500 sm:text-sm border-gray-300': !form.errors.type }"
                  >
                    <template v-if="form.settlementType">
                      <span class="block truncate">
                        <SettlementType :type="form.settlementType.value" />
                      </span>
                      <span class="flex absolute inset-y-0 right-0 items-center pr-2 pointer-events-none">
                        <ChevronUpDownIcon class="size-5 text-gray-400" />
                      </span>
                    </template>
                    <template v-else>
                      Ładowanie...
                    </template>
                  </ListboxButton>
                  <transition
                    leave-active-class="transition ease-in duration-100"
                    leave-from-class="opacity-100"
                    leave-to-class="opacity-0"
                  >
                    <ListboxOptions
                      class="overflow-auto absolute z-10 py-1 mt-1 w-full max-w-lg max-h-60 text-base bg-white rounded-md focus:outline-none ring-1 ring-black/5 shadow-lg sm:text-sm"
                    >
                      <ListboxOption
                        v-for="settlementType in settlementTypes"
                        :key="settlementType.value"
                        v-slot="{ active, selected }"
                        as="template"
                        :value="settlementType"
                      >
                        <li :class="[active ? 'bg-gray-100' : 'text-gray-900', 'cursor-default select-none relative py-2 pl-3 pr-9']">
                          <span :class="[selected ? 'font-semibold' : 'font-normal', 'block truncate']">
                            <SettlementType :type="settlementType.value" />
                          </span>

                          <span
                            v-if="selected"
                            :class="['text-blumilk-600 absolute inset-y-0 right-0 flex items-center pr-4']"
                          >
                            <CheckIcon class="size-5" />
                          </span>
                        </li>
                      </ListboxOption>
                    </ListboxOptions>
                  </transition>
                  <p
                    v-if="form.errors.type"
                    class="mt-2 text-sm text-red-600"
                  >
                    {{ form.errors.type }}
                  </p>
                </div>
              </Listbox>
              <div class="items-center py-4 sm:grid sm:grid-cols-3">
                <label
                  for="comment"
                  class="block text-sm font-medium text-gray-700"
                >
                  Komentarz
                  <span class="text-xs text-gray-500">
                    (opcjonalny)
                  </span>
                </label>
                <div class="mt-1 sm:col-span-2 sm:mt-0">
                  <textarea
                    id="comment"
                    v-model="form.comment"
                    rows="4"
                    class="block w-full max-w-lg rounded-md border-gray-300 focus:border-blumilk-500 focus:ring-blumilk-500 shadow-sm sm:text-sm"
                  />
                </div>
              </div>
            </div>
            <div class="flex justify-end py-3">
              <div class="space-x-3">
                <InertiaLink
                  href="/overtime/requests"
                  class="py-2 px-4 text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blumilk-500 focus:ring-offset-2 shadow-sm"
                >
                  Anuluj
                </InertiaLink>
                <button
                  type="submit"
                  class="inline-flex justify-center py-2 px-4 text-sm font-medium text-white bg-blumilk-600 rounded-md border border-transparent focus:outline-none focus:ring-2 focus:ring-blumilk-500 focus:ring-offset-2 shadow-sm"
                  :class="[form.processing || !isDirty ? 'disabled:opacity-60' : 'hover:bg-blumilk-700']"
                  :disabled="form.processing || !isDirty"
                >
                  Dodaj
                </button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>
