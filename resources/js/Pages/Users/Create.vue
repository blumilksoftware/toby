<template>
  <InertiaHead title="Dodawanie użytkownika" />
  <div class="bg-white sm:rounded-lg shadow-md">
    <div class="p-4 sm:px-6">
      <h2 class="text-lg leading-6 font-medium text-gray-900">
        Dodaj użytkownika
      </h2>
      <p class="mt-1 text-sm text-gray-500">
        Tylko dodani użytkownicy będą mogli zalogować się do aplikacji.
      </p>
    </div>
    <form
      class="border-t border-gray-200 px-6"
      @submit.prevent="createUser"
    >
      <div class="sm:grid sm:grid-cols-3 py-4 items-center">
        <label
          for="firstName"
          class="block text-sm font-medium text-gray-700 sm:mt-px"
        >
          Imię
        </label>
        <div class="mt-1 sm:mt-0 sm:col-span-2">
          <input
            id="firstName"
            v-model="form.firstName"
            type="text"
            class="block w-full max-w-lg shadow-sm rounded-md sm:text-sm"
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
      <div class="sm:grid sm:grid-cols-3 py-4 items-center">
        <label
          for="lastName"
          class="block text-sm font-medium text-gray-700 sm:mt-px"
        >
          Nazwisko
        </label>
        <div class="mt-1 sm:mt-0 sm:col-span-2">
          <input
            id="lastName"
            v-model="form.lastName"
            type="text"
            class="block w-full max-w-lg shadow-sm rounded-md sm:text-sm"
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
      <div class="sm:grid sm:grid-cols-3 py-4 items-center">
        <label
          for="email"
          class="block text-sm font-medium text-gray-700 sm:mt-px"
        >
          Adres e-mail
        </label>
        <div class="mt-1 sm:mt-0 sm:col-span-2">
          <input
            id="email"
            v-model="form.email"
            type="email"
            class="block w-full max-w-lg shadow-sm rounded-md sm:text-sm"
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
      <Listbox
        v-model="form.role"
        as="div"
        class="sm:grid sm:grid-cols-3 py-4 items-center"
      >
        <ListboxLabel class="block text-sm font-medium text-gray-700">
          Rola
        </ListboxLabel>
        <div class="mt-1 relative sm:mt-0 sm:col-span-2">
          <ListboxButton
            class="bg-white relative w-full max-w-lg border rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-default sm:text-sm focus:ring-1"
            :class="{ 'border-red-300 text-red-900 focus:outline-none focus:ring-red-500 focus:border-red-500': form.errors.employmentForm, 'focus:ring-blumilk-500 focus:border-blumilk-500 sm:text-sm border-gray-300': !form.errors.employmentForm }"
          >
            <span class="block truncate">{{ form.role.label }}</span>
            <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
              <SelectorIcon class="h-5 w-5 text-gray-400" />
            </span>
          </ListboxButton>
          <transition
            leave-active-class="transition ease-in duration-100"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
          >
            <ListboxOptions class="absolute z-10 mt-1 w-full max-w-lg bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm">
              <ListboxOption
                v-for="role in roles"
                :key="role.value"
                v-slot="{ active, selected }"
                as="template"
                :value="role"
              >
                <li :class="[active ? 'text-white bg-blumilk-600' : 'text-gray-900', 'cursor-default select-none relative py-2 pl-3 pr-9']">
                  <span :class="[selected ? 'font-semibold' : 'font-normal', 'block truncate']">
                    {{ role.label }}
                  </span>

                  <span
                    v-if="selected"
                    :class="[active ? 'text-white' : 'text-blumilk-600', 'absolute inset-y-0 right-0 flex items-center pr-4']"
                  >
                    <CheckIcon class="h-5 w-5" />
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
        class="sm:grid sm:grid-cols-3 py-4 items-center"
      >
        <ListboxLabel class="block text-sm font-medium text-gray-700">
          Forma zatrudnienia
        </ListboxLabel>
        <div class="mt-1 relative sm:mt-0 sm:col-span-2">
          <ListboxButton
            class="bg-white relative w-full max-w-lg border rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-default sm:text-sm focus:ring-1"
            :class="{ 'border-red-300 text-red-900 focus:outline-none focus:ring-red-500 focus:border-red-500': form.errors.employmentForm, 'focus:ring-blumilk-500 focus:border-blumilk-500 sm:text-sm border-gray-300': !form.errors.employmentForm }"
          >
            <span class="block truncate">{{ form.employmentForm.label }}</span>
            <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
              <SelectorIcon class="h-5 w-5 text-gray-400" />
            </span>
          </ListboxButton>

          <transition
            leave-active-class="transition ease-in duration-100"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
          >
            <ListboxOptions class="absolute z-10 mt-1 w-full max-w-lg bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm">
              <ListboxOption
                v-for="employmentForm in employmentForms"
                :key="employmentForm.value"
                v-slot="{ active, selected }"
                as="template"
                :value="employmentForm"
              >
                <li :class="[active ? 'text-white bg-blumilk-600' : 'text-gray-900', 'cursor-default select-none relative py-2 pl-3 pr-9']">
                  <span :class="[selected ? 'font-semibold' : 'font-normal', 'block truncate']">
                    {{ employmentForm.label }}
                  </span>

                  <span
                    v-if="selected"
                    :class="[active ? 'text-white' : 'text-blumilk-600', 'absolute inset-y-0 right-0 flex items-center pr-4']"
                  >
                    <CheckIcon class="h-5 w-5" />
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
      <div class="sm:grid sm:grid-cols-3 py-4 items-center">
        <label
          for="employment_date"
          class="block text-sm font-medium text-gray-700 sm:mt-px"
        >
          Data zatrudnienia
        </label>
        <div class="mt-1 sm:mt-0 sm:col-span-2">
          <FlatPickr
            id="employment_date"
            v-model="form.employmentDate"
            placeholder="Wybierz datę"
            class="block w-full max-w-lg shadow-sm rounded-md sm:text-sm"
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
      <div class="flex justify-end py-3">
        <div class="space-x-3">
          <InertiaLink
            href="/users"
            class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blumilk-500"
          >
            Anuluj
          </InertiaLink>
          <button
            type="submit"
            :disabled="form.processing"
            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blumilk-600 hover:bg-blumilk-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blumilk-500"
          >
            Zapisz
          </button>
        </div>
      </div>
    </form>
  </div>
</template>

<script>
import { useForm } from '@inertiajs/inertia-vue3'
import FlatPickr from 'vue-flatpickr-component'
import { Listbox, ListboxButton, ListboxLabel, ListboxOption, ListboxOptions } from '@headlessui/vue'
import { CheckIcon, SelectorIcon } from '@heroicons/vue/solid'

export default {
  name: 'UserCreate',
  components: {
    FlatPickr,
    Listbox,
    ListboxButton,
    ListboxLabel,
    ListboxOption,
    ListboxOptions,
    CheckIcon,
    SelectorIcon,
  },
  props: {
    employmentForms: {
      type: Object,
      default: () => null,
    },
    roles: {
      type: Object,
      default: () => null,
    },
  },
  setup(props) {
    const form = useForm({
      firstName: null,
      lastName: null,
      email: null,
      employmentForm: props.employmentForms[0],
      role: props.roles[0],
      employmentDate: null,
    })

    return { form }
  },
  methods: {
    createUser() {
      this.form
        .transform(data => ({
          ...data,
          employmentForm: data.employmentForm.value,
          role: data.role.value,
        }))
        .post('/users')
    },
  },
}
</script>
