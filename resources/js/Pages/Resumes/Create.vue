<template>
  <InertiaHead title="Dodawanie CV" />
  <div class="bg-white shadow-md">
    <div class="p-4 sm:px-6">
      <h2 class="text-lg font-medium leading-6 text-gray-900">
        Dodaj CV
      </h2>
    </div>
    <form
      class="px-6 border-t border-gray-200"
      @submit.prevent="createResume"
    >
      <h1 class="text-gray-900 font-medium mt-2">Informacje podstawowe</h1>
      <div class="items-center py-4 sm:grid sm:grid-cols-3">
        <label
          for="firstName"
          class="block text-sm font-medium text-gray-700 sm:mt-px"
        >
          Imię
        </label>
        <div class="mt-1 sm:col-span-2 sm:mt-0">
          <input
            id="firstName"
            v-model="form.firstName"
            type="text"
            class="block w-full max-w-lg rounded-md shadow-sm sm:text-sm"
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
      <div class="items-center py-4 sm:grid sm:grid-cols-3">
        <label
          for="lastName"
          class="block text-sm font-medium text-gray-700 sm:mt-px"
        >
          Nazwisko
        </label>
        <div class="mt-1 sm:col-span-2 sm:mt-0">
          <input
            id="lastName"
            v-model="form.lastName"
            type="text"
            class="block w-full max-w-lg rounded-md shadow-sm sm:text-sm"
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
      <div class="items-center py-4 sm:grid sm:grid-cols-3">
        <label
          class="block text-sm font-medium text-gray-700 sm:mt-px"
        >
          Języki
        </label>
        <div class="mt-1 sm:col-span-2 sm:mt-0">
          <p>Języki - taki bajer jak na JustJoinIT</p>
        </div>
      </div>
      <h1 class="text-gray-900 font-medium mt-4">Wykształcenie</h1>
      <div class="mt-2">
        <InertiaLink
          href="#"
          class="py-2 px-4 text-sm font-medium text-blumilk-700 bg-white hover:bg-blumilk-25 rounded-md border border-blumilk-300 focus:outline-none focus:ring-2 focus:ring-blumilk-500 focus:ring-offset-2 shadow-sm"
        >
          Dodaj wykształcenie
        </InertiaLink>
      </div>
      
      <div class="items-center py-4 sm:grid sm:grid-cols-3">
       <label
         for="university"
         class="block text-sm font-medium text-gray-700 sm:mt-px">
         Uczelnia
       </label>
       <div class="mt-1 sm:col-span-2 sm:mt-0">
         <input
           id="university"
           v-model="form.firstName"
           type="text"
           class="block w-full max-w-lg rounded-md shadow-sm sm:text-sm"
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
       <div class="items-center py-4 sm:grid sm:grid-cols-3">
         <label
           for="title"
           class="block text-sm font-medium text-gray-700 sm:mt-px"
         >
           Tytuł
         </label>
         <div class="mt-1 sm:col-span-2 sm:mt-0">
           <input
             id="title"
             v-model="form.firstName"
             type="text"
             class="block w-full max-w-lg rounded-md shadow-sm sm:text-sm"
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
     <div class="items-center py-4 sm:grid sm:grid-cols-3">
       <label
         for="subject"
         class="block text-sm font-medium text-gray-700 sm:mt-px"
       >
         Kierunek studiów
       </label>
       <div class="mt-1 sm:col-span-2 sm:mt-0">
         <input
           id="subject"
           v-model="form.firstName"
           type="text"
           class="block w-full max-w-lg rounded-md shadow-sm sm:text-sm"
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
      <div class="items-center py-4 sm:grid sm:grid-cols-3">
        <label
          for="subject"
          class="block text-sm font-medium text-gray-700 sm:mt-px"
        >
          Rok ukończenia
        </label>
        <div class="mt-1 sm:col-span-2 sm:mt-0">
          <input
            id="subject"
            v-model="form.firstName"
            type="text"
            class="block w-full max-w-lg rounded-md shadow-sm sm:text-sm"
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
      <div class="flex justify-end py-3">
        <div class="space-x-3">
          <InertiaLink
            href="/users"
            class="py-2 px-4 text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blumilk-500 focus:ring-offset-2 shadow-sm"
          >
            Anuluj
          </InertiaLink>
          <button
            type="submit"
            :disabled="form.processing"
            class="inline-flex justify-center py-2 px-4 text-sm font-medium text-white bg-blumilk-600 hover:bg-blumilk-700 rounded-md border border-transparent focus:outline-none focus:ring-2 focus:ring-blumilk-500 focus:ring-offset-2 shadow-sm"
          >
            Zapisz
          </button>
        </div>
      </div>
    </form>
  </div>
</template>

<script setup>
import { useForm } from '@inertiajs/inertia-vue3'
import FlatPickr from 'vue-flatpickr-component'
import { Listbox, ListboxButton, ListboxLabel, ListboxOption, ListboxOptions } from '@headlessui/vue'
import { CheckIcon, SelectorIcon } from '@heroicons/vue/solid'

const props = defineProps({
  employmentForms: Object,
  languages: Object,
})

const form = useForm({
  firstName: null,
  lastName: null,
  email: null,
  position: null,
  employmentDate: null,
})

function createResume() {
  form
    .transform(data => ({
      ...data,
      language: data.language.value,
    }))
    .post('/resumes')
}
</script>
