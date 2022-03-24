<template>
  <InertiaHead :title="error.title" />
  <div class="min-h-screen pt-16 pb-12 flex flex-col">
    <main class="flex-grow flex flex-col justify-center max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex-shrink-0 flex justify-center">
        <div>
          <InertiaLink
            href="/"
            class="inline-flex items-center"
          >
            <img
              class="block h-16 w-auto"
              src="/img/logo.png"
              alt="Logo"
            >
          </InertiaLink>
        </div>
      </div>
      <div class="py-8">
        <div class="text-center">
          <p class="text-7xl font-semibold text-blumilk-500 uppercase tracking-wide">
            {{ error.code }}
          </p>
          <h1 class="mt-2 text-2xl font-extrabold text-gray-900 tracking-tight">
            {{ error.title }}
          </h1>
          <p class="mt-2 text-base text-gray-500">
            {{ error.message }}
          </p>
          <div class="mt-6">
            <InertiaLink
              href="/"
              class="inline-flex items-center px-4 py-3 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-blumilk-600 hover:bg-blumilk-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blumilk-500"
            >
              Wróć do strony głównej
            </InertiaLink>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script>
import GuestLayout from '@/Shared/Layout/GuestLayout'

export default {
  name: 'ErrorPage',
  layout: GuestLayout,
  props: {
    status: Number,
  },
  computed: {
    error() {
      return {
        404: {
          code: '404',
          title: 'Błąd 404',
          message: 'Przykro nam, ale strona, której szukasz, nie istnieje.',
        },
      }[this.status]
    },

  },
}
</script>