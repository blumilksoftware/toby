<script setup>
import { useForm } from '@inertiajs/inertia-vue3'
import FlatPickr from 'vue-flatpickr-component'
import { Listbox, ListboxButton, ListboxLabel, ListboxOption, ListboxOptions } from '@headlessui/vue'
import { CheckIcon, ChevronUpDownIcon } from '@heroicons/vue/24/solid'

const props = defineProps({
  employmentForms: Object,
  roles: Object,
})

const form = useForm({
  firstName: null,
  lastName: null,
  email: null,
  employmentForm: props.employmentForms[0],
  role: props.roles[0],
  position: null,
  employmentDate: null,
  birthday: null,
  slackId: null,
})

function createUser() {
  form
    .transform(data => ({
      ...data,
      employmentForm: data.employmentForm.value,
      role: data.role.value,
    }))
    .post('/users')
}
</script>

<template>
  <InertiaHead title="Dodawanie użytkownika" />
  <div class="mx-auto w-full max-w-7xl bg-white shadow-md">
    <div class="p-4 sm:px-6">
      <h2 class="text-lg font-medium leading-6 text-gray-900">
        Dodaj użytkownika
      </h2>
    </div>
    <form
      class="px-6 border-t border-gray-200"
      @submit.prevent="createUser"
    >
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
          for="email"
          class="block text-sm font-medium text-gray-700 sm:mt-px"
        >
          Adres e-mail
        </label>
        <div class="mt-1 sm:col-span-2 sm:mt-0">
          <input
            id="email"
            v-model="form.email"
            type="email"
            class="block w-full max-w-lg rounded-md shadow-sm sm:text-sm"
            :class="{ 'border-red-300 text-red-900 focus:outline-none focus:ring-red-500 focus:border-red-500': form.errors.email, 'focus:ring-blumilk-500 focus:border-blumilk-500 sm:text-sm border-gray-300': !form.errors.email }"
          >
          <p
            v-if="form.errors.email"
            class="mt-2 text-sm text-red-600"
          >
            {{ form.errors.email }}
          </p>
        </div>
      </div>
      <div class="items-center py-4 sm:grid sm:grid-cols-3">
        <label
          for="position"
          class="block text-sm font-medium text-gray-700 sm:mt-px"
        >
          Stanowisko
        </label>
        <div class="mt-1 sm:col-span-2 sm:mt-0">
          <input
            id="position"
            v-model="form.position"
            type="text"
            class="block w-full max-w-lg rounded-md shadow-sm sm:text-sm"
            :class="{ 'border-red-300 text-red-900 focus:outline-none focus:ring-red-500 focus:border-red-500': form.errors.position, 'focus:ring-blumilk-500 focus:border-blumilk-500 sm:text-sm border-gray-300': !form.errors.position }"
          >
          <p
            v-if="form.errors.position"
            class="mt-2 text-sm text-red-600"
          >
            {{ form.errors.position }}
          </p>
        </div>
      </div>
      <Listbox
        v-model="form.role"
        as="div"
        class="items-center py-4 sm:grid sm:grid-cols-3"
      >
        <ListboxLabel class="block text-sm font-medium text-gray-700">
          Rola
        </ListboxLabel>
        <div class="relative mt-1 sm:col-span-2 sm:mt-0">
          <ListboxButton
            class="relative py-2 pr-10 pl-3 w-full max-w-lg text-left bg-white rounded-md border focus:outline-none focus:ring-1 shadow-sm cursor-default sm:text-sm"
            :class="{ 'border-red-300 text-red-900 focus:outline-none focus:ring-red-500 focus:border-red-500': form.errors.role, 'focus:ring-blumilk-500 focus:border-blumilk-500 sm:text-sm border-gray-300': !form.errors.role }"
          >
            <span class="block truncate">{{ form.role.label }}</span>
            <span class="flex absolute inset-y-0 right-0 items-center pr-2 pointer-events-none">
              <ChevronUpDownIcon class="w-5 h-5 text-gray-400" />
            </span>
          </ListboxButton>
          <transition
            leave-active-class="transition ease-in duration-100"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
          >
            <ListboxOptions class="overflow-auto absolute z-10 py-1 mt-1 w-full max-w-lg max-h-60 text-base bg-white rounded-md focus:outline-none ring-1 ring-black ring-opacity-5 shadow-lg sm:text-sm">
              <ListboxOption
                v-for="role in roles"
                :key="role.value"
                v-slot="{ active, selected }"
                as="template"
                :value="role"
              >
                <li :class="[active ? 'bg-gray-100' : 'text-gray-900', 'cursor-default select-none relative py-2 pl-3 pr-9']">
                  <span :class="[selected ? 'font-semibold' : 'font-normal', 'block truncate']">
                    {{ role.label }}
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
            v-if="form.errors.role"
            class="mt-2 text-sm text-red-600"
          >
            {{ form.errors.role }}
          </p>
        </div>
      </Listbox>
      <Listbox
        v-model="form.employmentForm"
        as="div"
        class="items-center py-4 sm:grid sm:grid-cols-3"
      >
        <ListboxLabel class="block text-sm font-medium text-gray-700">
          Forma zatrudnienia
        </ListboxLabel>
        <div class="relative mt-1 sm:col-span-2 sm:mt-0">
          <ListboxButton
            class="relative py-2 pr-10 pl-3 w-full max-w-lg text-left bg-white rounded-md border focus:outline-none focus:ring-1 shadow-sm cursor-default sm:text-sm"
            :class="{ 'border-red-300 text-red-900 focus:outline-none focus:ring-red-500 focus:border-red-500': form.errors.employmentForm, 'focus:ring-blumilk-500 focus:border-blumilk-500 sm:text-sm border-gray-300': !form.errors.employmentForm }"
          >
            <span class="block truncate">{{ form.employmentForm.label }}</span>
            <span class="flex absolute inset-y-0 right-0 items-center pr-2 pointer-events-none">
              <ChevronUpDownIcon class="w-5 h-5 text-gray-400" />
            </span>
          </ListboxButton>

          <transition
            leave-active-class="transition ease-in duration-100"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
          >
            <ListboxOptions class="overflow-auto absolute z-10 py-1 mt-1 w-full max-w-lg max-h-60 text-base bg-white rounded-md focus:outline-none ring-1 ring-black ring-opacity-5 shadow-lg sm:text-sm">
              <ListboxOption
                v-for="employmentForm in employmentForms"
                :key="employmentForm.value"
                v-slot="{ active, selected }"
                as="template"
                :value="employmentForm"
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
          for="employment_date"
          class="block text-sm font-medium text-gray-700 sm:mt-px"
        >
          Data zatrudnienia
        </label>
        <div
          class="mt-1 sm:col-span-2 sm:mt-0"
          dusk="employment-date"
        >
          <FlatPickr
            id="employment_date"
            v-model="form.employmentDate"
            class="block w-full max-w-lg rounded-md shadow-sm sm:text-sm"
            :class="{ 'border-red-300 text-red-900 focus:outline-none focus:ring-red-500 focus:border-red-500': form.errors.employmentDate, 'focus:ring-blumilk-500 focus:border-blumilk-500 sm:text-sm border-gray-300': !form.errors.employmentDate }"
          />
          <p
            v-if="form.errors.employmentDate"
            class="mt-2 text-sm text-red-600"
          >
            {{ form.errors.employmentDate }}
          </p>
        </div>
      </div>
      <div class="items-center py-4 sm:grid sm:grid-cols-3">
        <label
          for="slackId"
          class="block text-sm font-medium text-gray-700 sm:mt-px"
        >
          Slack ID
        </label>
        <div class="mt-1 sm:col-span-2 sm:mt-0">
          <input
            id="slack"
            v-model="form.slackId"
            type="text"
            class="block w-full max-w-lg rounded-md shadow-sm sm:text-sm"
            :class="{ 'border-red-300 text-red-900 focus:outline-none focus:ring-red-500 focus:border-red-500': form.errors.slackId, 'focus:ring-blumilk-500 focus:border-blumilk-500 sm:text-sm border-gray-300': !form.errors.slackId }"
          >
          <p
            v-if="form.errors.slackId"
            class="mt-2 text-sm text-red-600"
          >
            {{ form.errors.slackId }}
          </p>
        </div>
      </div>
      <div class="items-center py-4 sm:grid sm:grid-cols-3">
        <label
          for="birthday"
          class="block text-sm font-medium text-gray-700 sm:mt-px"
        >
          Data urodzenia
        </label>
        <div
          class="mt-1 sm:col-span-2 sm:mt-0"
          dusk="birthday"
        >
          <FlatPickr
            id="birthday"
            v-model="form.birthday"
            class="block w-full max-w-lg rounded-md shadow-sm sm:text-sm"
            :class="{ 'border-red-300 text-red-900 focus:outline-none focus:ring-red-500 focus:border-red-500': form.errors.birthday, 'focus:ring-blumilk-500 focus:border-blumilk-500 sm:text-sm border-gray-300': !form.errors.birthday }"
          />
          <p
            v-if="form.errors.birthday"
            class="mt-2 text-sm text-red-600"
          >
            {{ form.errors.birthday }}
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
            class="inline-flex justify-center py-2 px-4 text-sm font-medium text-white bg-blumilk-600 rounded-md border border-transparent focus:outline-none focus:ring-2 focus:ring-blumilk-500 focus:ring-offset-2 shadow-sm"
            :class="[form.processing || !form.isDirty ? 'disabled:opacity-60' : 'hover:bg-blumilk-700']"
            :disabled="form.processing || !form.isDirty"
            dusk="save-user-button"
          >
            Dodaj
          </button>
        </div>
      </div>
    </form>
  </div>
</template>
