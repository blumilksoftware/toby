<script setup>
import { XMarkIcon, ExclamationTriangleIcon } from '@heroicons/vue/24/solid'
import { ref } from 'vue'

defineProps({
  errors: Object,
  showLocalLoginButton: Boolean,
})

const showError = ref(true)
</script>

<script>
import GuestLayout from '@/Shared/Layout/GuestLayout.vue'

export default { name: 'LoginPage', layout: GuestLayout }
</script>

<template>
  <InertiaHead title="Zaloguj się" />
  <transition
    enter-active-class="transform ease-out duration-300 transition"
    enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
    enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
    leave-active-class="transition ease-in duration-100"
    leave-from-class="opacity-100"
    leave-to-class="opacity-0"
  >
    <div
      v-if="errors.oauth && showError"
      class="overflow-hidden absolute inset-x-2 top-2 bg-red-500 rounded-lg ring-1 ring-black ring-opacity-5 shadow-lg pointer-events-auto sm:mx-auto sm:w-full sm:max-w-md"
    >
      <div class="p-4">
        <div class="flex items-center">
          <div class="flex flex-1 justify-between w-0">
            <ExclamationTriangleIcon class="mr-1 w-5 h-5 text-white" />
            <p class="flex-1 w-0 text-sm font-medium text-white">
              {{ errors.oauth }}
            </p>
          </div>
          <div class="flex shrink-0 ml-4">
            <button
              class="inline-flex text-red-100 hover:text-red-400 bg-red-500 rounded-md focus:outline-none focus:ring-2 focus:ring-red-600 focus:ring-offset-2"
              @click="showError = false"
            >
              <span class="sr-only">Close</span>
              <XMarkIcon class="w-5 h-5" />
            </button>
          </div>
        </div>
      </div>
    </div>
  </transition>
  <div
    class="flex flex-col items-center py-8 px-4 space-y-8 text-white rounded-lg sm:mx-auto sm:w-full sm:max-w-md"
    dusk="login-link"
  >
    <img
      class="mx-auto w-auto h-50"
      src="images/logo.svg"
    >
    <a
      href="/login/google/start"
      class="inline-flex justify-center items-center py-2 px-6 font-medium text-white bg-blumilk-500 hover:bg-blumilk-700 rounded-md shadow-sm text-md"
    >
      Zaloguj się za pomocą Google
      <svg
        class="ml-2 w-5 h-5"
        fill="currentColor"
        viewBox="0 0 24 24"
      >
        <path
          d="M6 12C6 15.3137 8.68629 18 12 18C14.6124 18 16.8349 16.3304 17.6586 14H12V10H21.8047V14H21.8C20.8734 18.5645 16.8379 22 12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2C15.445 2 18.4831 3.742 20.2815 6.39318L17.0039 8.68815C15.9296 7.06812 14.0895 6 12 6C8.68629 6 6 8.68629 6 12Z"
          fill="currentColor"
        />
      </svg>
    </a>
    <a
      v-if="showLocalLoginButton"
      href="/login/local"
      class="inline-flex justify-center items-center py-2 px-6 font-medium text-white bg-blumilk-500 hover:bg-blumilk-700 rounded-md shadow-sm text-md"
    >
      Zaloguj się za pomocą hasła
    </a>
  </div>
</template>
