<template>
  <InertiaHead title="Dodawanie CV" />
  <div class="mx-auto w-full max-w-7xl bg-white shadow-md">
    <div class="p-4 sm:px-6">
      <h2 class="text-lg font-medium leading-6 text-gray-900">
        Dodaj CV
      </h2>
    </div>
    <form class="flex flex-col justify-center py-8 px-6 space-y-8 border-t border-gray-200 divide-y divide-gray-200">
      <div class="space-y-8 sm:space-y-5">
        <div>
          <h3 class="text-lg font-medium leading-6 text-gray-900">
            Dane podstawowe
          </h3>
          <div class="pt-4">
            <div class="items-center py-4 sm:grid sm:grid-cols-2">
              <label
                for="firstName"
                class="block text-sm font-medium text-gray-700 sm:mt-px"
              >
                Imię
              </label>
              <div class="mt-1 sm:mt-0">
                <input
                  id="firstName"
                  v-model="form.firstName"
                  type="text"
                  class="block w-full rounded-md shadow-sm sm:text-sm"
                  :class="{ 'border-red-300 text-red-900 focus:outline-none focus:ring-red-500 focus:border-red-500': form.errors.firstName, 'focus:ring-blumilk-500 focus:border-blumilk-500 sm:text-sm border-gray-300': !form.errors.firstName }"
                >
                <p
                  v-if="form.errors.firstName"
                  class="mt-2 text-sm text-red-600"
                >
                  {{ form.errors.firstName }}
                </p>
              </div>
            </div>
            <div class="items-center py-4 sm:grid sm:grid-cols-2">
              <label
                for="lastName"
                class="block text-sm font-medium text-gray-700 sm:mt-px"
              >
                Nazwisko
              </label>
              <div class="mt-1 sm:mt-0">
                <input
                  id="lastName"
                  v-model="form.lastName"
                  type="text"
                  class="block w-full rounded-md shadow-sm sm:text-sm"
                  :class="{ 'border-red-300 text-red-900 focus:outline-none focus:ring-red-500 focus:border-red-500': form.errors.lastName, 'focus:ring-blumilk-500 focus:border-blumilk-500 sm:text-sm border-gray-300': !form.errors.lastName }"
                >
                <p
                  v-if="form.errors.lastName"
                  class="mt-2 text-sm text-red-600"
                >
                  {{ form.errors.lastName }}
                </p>
              </div>
            </div>
          </div>
        </div>
        <div>
          <h3 class="text-lg font-medium leading-6 text-gray-900">
            Edukacja
          </h3>
          <div class="pt-4 space-y-4">
            <div
              v-for="(element, index) in form.elements"
              :key="index"
              class="flex items-start"
            >
              <Disclosure
                v-slot="{ open }"
                default-open
                as="div"
                class="flex-1 border border-gray-200"
              >
                <div class="flex">
                  <DisclosureButton :class="['transition transition-colors rounded-md group w-full max-w-full overflow-hidden flex items-center justify-between p-4 font-semibold text-gray-500 hover:text-blumilk-500 transition transition-colors rounded-md focus:outline-none']">
                    <div class="break-all line-clamp-1 text-md">
                      {{ element.school ? element.school : '(Nieokreślony)' }}
                    </div>
                    <div class="ml-2">
                      <svg
                        :class="[open ? '-rotate-90' : 'rotate-90', 'h-6 w-6 transform transition-transform ease-in-out duration-150']"
                        viewBox="0 0 20 20"
                      >
                        <path
                          d="M6 6L14 10L6 14V6Z"
                          fill="currentColor"
                        />
                      </svg>
                    </div>
                  </DisclosureButton>
                </div>
                <DisclosurePanel
                  as="div"
                  class="py-2 px-4 border-t border-gray-200"
                >
                  <div>
                    <div class="items-center py-4 sm:grid sm:grid-cols-2">
                      <label
                        :for="`school-${index}`"
                        class="block text-sm font-medium text-gray-700 sm:mt-px"
                      >
                        Szkoła
                      </label>
                      <div class="mt-1 sm:mt-0">
                        <input
                          :id="`school-${index}`"
                          v-model="element.school"
                          type="text"
                          class="block w-full rounded-md shadow-sm sm:text-sm"
                          :class="{ 'border-red-300 text-red-900 focus:outline-none focus:ring-red-500 focus:border-red-500': form.errors[`elements.${index}.school`], 'focus:ring-blumilk-500 focus:border-blumilk-500 sm:text-sm border-gray-300': !form.errors[`elements.${index}.school`] }"
                        >
                        <p
                          v-if="form.errors[`elements.${index}.school`]"
                          class="mt-2 text-sm text-red-600"
                        >
                          {{ form.errors[`elements.${index}.school`] }}
                        </p>
                      </div>
                    </div>
                    <div class="items-center py-4 sm:grid sm:grid-cols-2">
                      <label
                        :for="`degree-${index}`"
                        class="block text-sm font-medium text-gray-700 sm:mt-px"
                      >
                        Stopień
                      </label>
                      <div class="mt-1 sm:mt-0">
                        <input
                          :id="`degree-${index}`"
                          v-model="element.degree"
                          type="text"
                          class="block w-full rounded-md shadow-sm sm:text-sm"
                          :class="{ 'border-red-300 text-red-900 focus:outline-none focus:ring-red-500 focus:border-red-500': form.errors[`elements.${index}.degree`], 'focus:ring-blumilk-500 focus:border-blumilk-500 sm:text-sm border-gray-300': !form.errors[`elements.${index}.degree`] }"
                        >
                        <p
                          v-if="form.errors[`elements.${index}.degree`]"
                          class="mt-2 text-sm text-red-600"
                        >
                          {{ form.errors[`elements.${index}.degree`] }}
                        </p>
                      </div>
                    </div>
                    <div class="items-center py-4 sm:grid sm:grid-cols-2">
                      <label
                        :for="`fieldOfStudy-${index}`"
                        class="block text-sm font-medium text-gray-700 sm:mt-px"
                      >
                        Kierunek/Specjalizacja
                      </label>
                      <div class="mt-1 sm:mt-0">
                        <input
                          :id="`fieldOfStudy-${index}`"
                          v-model="element.fieldOfStudy"
                          type="text"
                          class="block w-full rounded-md shadow-sm sm:text-sm"
                          :class="{ 'border-red-300 text-red-900 focus:outline-none focus:ring-red-500 focus:border-red-500': form.errors[`elements.${index}.fieldOfStudy`], 'focus:ring-blumilk-500 focus:border-blumilk-500 sm:text-sm border-gray-300': !form.errors[`elements.${index}.fieldOfStudy`] }"
                        >
                        <p
                          v-if="form.errors[`elements.${index}.fieldOfStudy`]"
                          class="mt-2 text-sm text-red-600"
                        >
                          {{ form.errors[`elements.${index}.fieldOfStudy`] }}
                        </p>
                      </div>
                    </div>
                    <div class="items-center py-4 sm:grid sm:grid-cols-2">
                      <label
                        :for="`startDate-${index}`"
                        class="block text-sm font-medium text-gray-700 sm:mt-px"
                      >
                        Data rozpoczęcia
                      </label>
                      <div class="mt-1 sm:mt-0">
                        <FlatPickr
                          :id="`startDate-${index}`"
                          v-model="form.startDate"
                          placeholder="Wybierz datę"
                          class="block w-full rounded-md shadow-sm sm:text-sm"
                          :class="{ 'border-red-300 text-red-900 focus:outline-none focus:ring-red-500 focus:border-red-500': form.errors[`elements.${index}.startDate`], 'focus:ring-blumilk-500 focus:border-blumilk-500 sm:text-sm border-gray-300': !form.errors[`elements.${index}.startDate`] }"
                        />
                        <p
                          v-if="form.errors[`elements.${index}.startDate`]"
                          class="mt-2 text-sm text-red-600"
                        >
                          {{ form.errors[`elements.${index}.startDate`] }}
                        </p>
                      </div>
                    </div>
                    <div class="items-center py-4 sm:grid sm:grid-cols-2">
                      <label
                        :for="`endDate-${index}`"
                        class="block text-sm font-medium text-gray-700 sm:mt-px"
                      >
                        Data zakończenia
                      </label>
                      <div class="mt-1 sm:mt-0">
                        <FlatPickr
                          :id="`endDate-${index}`"
                          v-model="form.endDate"
                          placeholder="Wybierz datę"
                          class="block w-full rounded-md shadow-sm sm:text-sm"
                          :class="{ 'border-red-300 text-red-900 focus:outline-none focus:ring-red-500 focus:border-red-500': form.errors[`elements.${index}.endDate`], 'focus:ring-blumilk-500 focus:border-blumilk-500 sm:text-sm border-gray-300': !form.errors[`elements.${index}.endDate`] }"
                        />
                        <p
                          v-if="form.errors[`elements.${index}.endDate`]"
                          class="mt-2 text-sm text-red-600"
                        >
                          {{ form.errors[`elements.${index}.endDate`] }}
                        </p>
                      </div>
                    </div>
                  </div>
                </DisclosurePanel>
              </Disclosure>
              <button
                class="py-4 pl-4 text-red-500 hover:text-red-600 hover:scale-110"
                type="button"
                @click="form.elements.splice(index, 1)"
              >
                <TrashIcon class="w-5 h-5 text-red-500" />
              </button>
            </div>
            <button
              type="button"
              class="p-4 w-full font-semibold text-center text-blumilk-600 hover:bg-blumilk-25 focus:outline-none transition-colors"
              @click="form.elements.push({
                school: null,
                degree: null,
                fieldOfStudy: null,
                startDate: null,
                endDate: null,
              })"
            >
              Dodaj element
            </button>
          </div>
        </div>
      </div>

      <div class="pt-5">
        <div class="flex justify-end">
          <button
            type="button"
            class="py-2 px-4 text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 shadow-sm"
          >
            Cancel
          </button>
          <button
            type="submit"
            class="inline-flex justify-center py-2 px-4 ml-3 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-md border border-transparent focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 shadow-sm"
          >
            Save
          </button>
        </div>
      </div>
    </form>
  </div>
</template>

<script setup>
import {
  Disclosure,
  DisclosureButton,
  DisclosurePanel,
} from '@headlessui/vue'
import { TrashIcon } from '@heroicons/vue/outline'
import { useForm } from '@inertiajs/inertia-vue3'
import FlatPickr from 'vue-flatpickr-component'

const form = useForm({
  elements: [{
    school: null,
    degree: null,
    fieldOfStudy: null,
    startDate: null,
    endDate: null,
  }],
})
</script>