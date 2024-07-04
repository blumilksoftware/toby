<script setup>
import { useForm } from '@inertiajs/inertia-vue3'
import FlatPickr from 'vue-flatpickr-component'
import { Listbox, ListboxButton, ListboxLabel, ListboxOption, ListboxOptions } from '@headlessui/vue'
import { CheckIcon, ChevronUpDownIcon } from '@heroicons/vue/24/solid'

const props = defineProps({
  employmentForms: Array,
  types: Array,
  userId: Number,
})

const form = useForm({
  from: props.vacationFromDate,
  to: props.vacationFromDate,
  comment: '',
  type: '',
  employmentForm: '',
})

function createForm() {
  form.transform(data => ({
    employmentForm: data.employmentForm?.value,
    comment: data.comment,
    type: data.type.value,
    from: data.from,
    to: data.to,
  }))
    .post(`/users/${props.userId}/history`)
}

</script>

<template>
  <InertiaHead title="Dodaj wpis" />
  <div class="mx-auto w-full max-w-7xl">
    <div class="flex flex-col h-full bg-white shadow-md xl:col-span-2">
      <div class="p-4 sm:px-6">
        <h2 class="text-lg font-medium leading-6 text-gray-900">
          Dodaj wpis
        </h2>
      </div>
      <form
        class="px-6 h-full border-t border-gray-200"
        @submit.prevent="createForm"
      >
        <div class="flex flex-col justify-around h-full">
          <div>
            <Listbox
              v-model="form.type"
              as="div"
              class="items-center py-4 sm:grid sm:grid-cols-3"
            >
              <ListboxLabel class="block text-sm font-medium text-gray-700">
                Typ wpisu
              </ListboxLabel>
              <div class="relative mt-1 sm:col-span-2 sm:mt-0">
                <ListboxButton
                  :class="{ 'border-red-300 text-red-900 focus:ring-red-500 focus:border-red-500': form.errors.type, 'focus:ring-blumilk-500 focus:border-blumilk-500 sm:text-sm border-gray-300': !form.errors.type }"
                  class="relative py-2 pr-10 pl-3 w-full max-w-lg text-left bg-white rounded-md border focus:outline-none focus:ring-1 shadow-sm cursor-default sm:text-sm"
                >
                  <template v-if="form.type">
                    <span class="block truncate">
                      {{ form.type.label }}
                    </span>
                    <span class="flex absolute inset-y-0 right-0 items-center pr-2 pointer-events-none">
                      <ChevronUpDownIcon class="w-5 h-5 text-gray-400" />
                    </span>
                  </template>
                  <template v-else>
                    <span class="!text-gray-400">Wybierz opcję</span>
                    <span class="flex absolute inset-y-0 right-0 items-center pr-2 pointer-events-none">
                      <ChevronUpDownIcon class="w-5 h-5 text-gray-400" />
                    </span>
                  </template>
                </ListboxButton>
                <transition
                  leave-active-class="transition ease-in duration-100"
                  leave-from-class="opacity-100"
                  leave-to-class="opacity-0"
                >
                  <ListboxOptions
                    class="overflow-auto absolute z-10 py-1 mt-1 w-full max-w-lg max-h-60 text-base bg-white rounded-md focus:outline-none ring-1 ring-black ring-opacity-5 shadow-lg sm:text-sm"
                  >
                    <ListboxOption
                      v-for="type in types"
                      :key="type.value"
                      v-slot="{ active, selected }"
                      :value="type"
                      as="template"
                    >
                      <li :class="[active ? 'bg-gray-100' : 'text-gray-900', 'cursor-default select-none relative py-2 pl-3 pr-9']">
                        <span :class="[selected ? 'font-semibold' : 'font-normal', 'block truncate']">
                          {{ type.label }}
                        </span>

                        <span
                          v-if="selected"
                          :class="['text-blumilk-600 absolute inset-y-0 right-0 flex items-center pr-4']"
                        >
                          <CheckIcon class="w-5 h-5" />
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
            <Listbox
              v-if="form.type?.value === 'employment'"
              v-model="form.employmentForm"
              as="div"
              class="items-center py-4 sm:grid sm:grid-cols-3"
            >
              <ListboxLabel class="block text-sm font-medium text-gray-700">
                Forma zatrudnienia
              </ListboxLabel>
              <div class="relative mt-1 sm:col-span-2 sm:mt-0">
                <ListboxButton
                  :class="{ 'border-red-300 text-red-900 focus:ring-red-500 focus:border-red-500': form.errors.employmentForm, 'focus:ring-blumilk-500 focus:border-blumilk-500 sm:text-sm border-gray-300': !form.errors.type }"
                  class="relative py-2 pr-10 pl-3 w-full max-w-lg text-left bg-white rounded-md border focus:outline-none focus:ring-1 shadow-sm cursor-default sm:text-sm"
                >
                  <template v-if="form.employmentForm">
                    <span class="block truncate">
                      {{ form.employmentForm.label }}
                    </span>
                    <span class="flex absolute inset-y-0 right-0 items-center pr-2 pointer-events-none">
                      <ChevronUpDownIcon class="w-5 h-5 text-gray-400" />
                    </span>
                  </template>
                  <template v-else>
                    <span class="!text-gray-400">Wybierz opcję</span>
                    <span class="flex absolute inset-y-0 right-0 items-center pr-2 pointer-events-none">
                      <ChevronUpDownIcon class="w-5 h-5 text-gray-400" />
                    </span>
                  </template>
                </ListboxButton>
                <transition
                  leave-active-class="transition ease-in duration-100"
                  leave-from-class="opacity-100"
                  leave-to-class="opacity-0"
                >
                  <ListboxOptions
                    class="overflow-auto absolute z-10 py-1 mt-1 w-full max-w-lg max-h-60 text-base bg-white rounded-md focus:outline-none ring-1 ring-black ring-opacity-5 shadow-lg sm:text-sm"
                  >
                    <ListboxOption
                      v-for="employmentForm in employmentForms"
                      :key="employmentForm.value"
                      v-slot="{ active, selected }"
                      :value="employmentForm"
                      as="template"
                    >
                      <li :class="[active ? 'bg-gray-100' : 'text-gray-900', 'cursor-default select-none relative py-2 pl-3 pr-9']">
                        <span :class="[selected ? 'font-semibold' : 'font-normal', 'block truncate']">
                          {{ employmentForm.label }}
                        </span>

                        <span
                          v-if="selected"
                          :class="['text-blumilk-600 absolute inset-y-0 right-0 flex items-center pr-4']"
                        >
                          <CheckIcon class="w-5 h-5" />
                        </span>
                      </li>
                    </ListboxOption>
                  </ListboxOptions>
                </transition>
                <p
                  v-if="form.errors.employmentForm"
                  class="mt-2 text-sm text-red-600"
                >
                  {{ form.errors.employmentForm }}
                </p>
              </div>
            </Listbox>
            <div class="items-center py-4 sm:grid sm:grid-cols-3">
              <label
                class="block text-sm font-medium text-gray-700 sm:mt-px"
                for="date_from"
              >
                Data od
              </label>
              <div class="mt-1 sm:col-span-2 sm:mt-0">
                <FlatPickr
                  id="date_from"
                  v-model="form.from"
                  :class="{ 'border-red-300 text-red-900 focus:outline-none focus:ring-red-500 focus:border-red-500': form.errors.from, 'focus:ring-blumilk-500 focus:border-blumilk-500 sm:text-sm border-gray-300': !form.errors.from }"
                  class="block w-full max-w-lg rounded-md shadow-sm sm:text-sm"
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
                class="block text-sm font-medium text-gray-700 sm:mt-px"
                for="date_from"
              >
                Data do
              </label>
              <div class="mt-1 sm:col-span-2 sm:mt-0">
                <FlatPickr
                  id="date_to"
                  v-model="form.to"
                  :class="{ 'border-red-300 text-red-900 focus:outline-none focus:ring-red-500 focus:border-red-500': form.errors.to, 'focus:ring-blumilk-500 focus:border-blumilk-500 sm:text-sm border-gray-300': !form.errors.to }"
                  class="block w-full max-w-lg rounded-md shadow-sm sm:text-sm"
                />
                <p
                  v-if="form.errors.to"
                  class="mt-2 text-sm text-red-600"
                >
                  {{ form.errors.to }}
                </p>
              </div>
            </div>
          </div>
          <div class="items-center py-4 sm:grid sm:grid-cols-3">
            <label
              for="comment"
              class="block text-sm font-medium text-gray-700 sm:mt-px"
            >
              Komentarz
              <span class="text-xs text-gray-500">
                (opcjonalny)
              </span>
            </label>
            <div class="mt-1 sm:col-span-2 sm:mt-0">
              <input
                id="comment"
                v-model="form.comment"
                type="text"
                class="block w-full max-w-lg rounded-md shadow-sm sm:text-sm"
                :class="{ 'border-red-300 text-red-900 focus:outline-none focus:ring-red-500 focus:border-red-500': form.errors.comment, 'focus:ring-blumilk-500 focus:border-blumilk-500 sm:text-sm border-gray-300': !form.errors.comment }"
              >
              <p
                v-if="form.errors.comment"
                class="mt-2 text-sm text-red-600"
              >
                {{ form.errors.comment }}
              </p>
            </div>
          </div>
          <div class="flex justify-end py-3">
            <div class="space-x-3">
              <InertiaLink
                class="py-2 px-4 text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blumilk-500 focus:ring-offset-2 shadow-sm"
                :href="`/users/${props.userId}/history`"
              >
                Anuluj
              </InertiaLink>
              <button
                :class="[form.processing ? 'disabled:opacity-60' : 'hover:bg-blumilk-700']"
                :disabled="form.processing"
                class="inline-flex justify-center py-2 px-4 text-sm font-medium text-white bg-blumilk-600 rounded-md border border-transparent focus:outline-none focus:ring-2 focus:ring-blumilk-500 focus:ring-offset-2 shadow-sm"
                type="submit"
              >
                Dodaj
              </button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>
