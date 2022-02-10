<template>
  <InertiaHead title="Zaloguj się" />
  <div
    class="sm:mx-auto sm:w-full sm:max-w-md text-white space-y-4 flex flex-col items-center rounded-lg px-4 py-8"
    dusk="login-link"
  >
    <img
      class="mx-auto h-50 w-auto"
      src="img/logo.png"
      alt="Blumilk"
    >
    <a
      href="/login/google/start"
      class="inline-flex justify-center py-2 px-6 rounded-md shadow-sm bg-blumilk-500 text-md font-medium text-white hover:bg-blumilk-700"
    >
      Zaloguj się za pomocą Google
      <svg
        class="w-5 h-5 ml-2"
        fill="currentColor"
        viewBox="0 0 24 24"
      >
        <path
          d="M6 12C6 15.3137 8.68629 18 12 18C14.6124 18 16.8349 16.3304 17.6586 14H12V10H21.8047V14H21.8C20.8734 18.5645 16.8379 22 12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2C15.445 2 18.4831 3.742 20.2815 6.39318L17.0039 8.68815C15.9296 7.06812 14.0895 6 12 6C8.68629 6 6 8.68629 6 12Z"
          fill="currentColor"
        />
      </svg>
    </a>
  </div>
</template>

<script>
import GuestLayout from '@/Shared/Layout/GuestLayout'

export default {
  name: 'LoginPage',

  layout: GuestLayout,
}
</script>
