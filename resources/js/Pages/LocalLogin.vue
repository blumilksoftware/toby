<script setup>
import {useForm} from "@inertiajs/inertia-vue3";

const form = useForm({
  email: null,
  password: null,
})

function localLogin() {
  form.post('/login/local')
}
</script>

<script>
import GuestLayout from '@/Shared/Layout/GuestLayout.vue'

export default {name: 'LoginPage', layout: GuestLayout}
</script>

<template>
  <InertiaHead title="Zaloguj się"/>
  <div
      class="flex flex-col items-center py-8 px-4 space-y-8 text-white rounded-lg sm:mx-auto sm:w-full sm:max-w-md"
      dusk="login-link"
  >
    <img
        class="mx-auto w-auto h-20"
        src="/images/logo.svg"
    >
    <div class="mx-auto w-full max-w-7xl bg-white shadow-md">
      <div class="p-4 sm:px-6">
        <h2 class="text-lg font-medium leading-6 text-gray-900">
          Zaloguj się
        </h2>
      </div>
      <form
          class="px-6 border-t border-gray-200"
          @submit.prevent="localLogin"
      >
        <div class="items-center py-4 sm:grid sm:grid-cols-3/">
          <label
              for="email"
              class="block text-sm font-medium text-gray-700 sm:mt-px"
          >
            Adres e-mail
          </label>
          <div class="mt-2">
            <input
                id="email"
                v-model="form.email"
                type="email"
                class="block w-full max-w-lg rounded-md shadow-sm sm:text-sm text-black"
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
        <div class="items-center sm:grid sm:grid-cols-3/">
          <label
              for="email"
              class="block text-sm font-medium text-gray-700 sm:mt-px"
          >
            Hasło
          </label>
          <div class="mt-2">
            <input
                id="password"
                v-model="form.password"
                type="password"
                class="block w-full max-w-lg rounded-md shadow-sm sm:text-sm text-black"
                :class="{ 'border-red-300 text-red-900 focus:outline-none focus:ring-red-500 focus:border-red-500': form.errors.password, 'focus:ring-blumilk-500 focus:border-blumilk-500 sm:text-sm border-gray-300': !form.errors.password }"
            >
            <p
                v-if="form.errors.password"
                class="mt-2 text-sm text-red-600"
            >
              {{ form.errors.password }}
            </p>
          </div>
        </div>
        <div class="py-4 mt-2">
          <button
              type="submit"
              class="w-full inline-flex justify-center py-2 px-4 text-sm font-medium text-white bg-blumilk-600 rounded-md border border-transparent focus:outline-none focus:ring-2 focus:ring-blumilk-500 focus:ring-offset-2 shadow-sm"
              :class="[form.processing || !form.isDirty ? 'disabled:opacity-60' : 'hover:bg-blumilk-700']"
              :disabled="form.processing || !form.isDirty"
          >
            Zaloguj
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
