<template>
  <InertiaHead :title="error.title" />
  <div class="py-16 px-4 min-h-full sm:py-24 sm:px-6 md:grid md:place-items-center lg:px-8">
    <div class="mx-auto max-w-max">
      <main class="sm:flex">
        <p class="text-4xl font-extrabold text-blumilk-600 sm:text-5xl">
          {{ error.code }}
        </p>
        <div class="sm:ml-6">
          <div class="sm:pl-6 sm:border-l sm:border-gray-200">
            <h1 class="text-4xl font-extrabold tracking-tight text-gray-900 sm:text-5xl">
              {{ error.title }}
            </h1>
            <p class="mt-1 text-base text-gray-500">
              {{ error.message }}
            </p>
          </div>
          <div class="flex mt-10 space-x-3 sm:pl-6 sm:border-l sm:border-transparent">
            <InertiaLink
              href="/"
              class="inline-flex items-center py-3 px-4 text-sm font-medium leading-4 text-white bg-blumilk-600 hover:bg-blumilk-700 rounded-md border border-transparent focus:outline-none focus:ring-2 focus:ring-blumilk-500 focus:ring-offset-2 shadow-sm"
            >
              Wróć do strony głównej
            </InertiaLink>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>

<script>
import GuestLayout from '@/Shared/Layout/GuestLayout.vue'

export default {
  name: 'ErrorPage',
  layout: GuestLayout,
  props: {
    status: Number,
  },
  computed: {
    error() {
      return {
        401: {
          code: '401',
          title: 'Nieuprawniony dostęp',
          message: 'Aby mieć dostęp do tej strony, musisz się zalogować.',
        },
        403: {
          code: '403',
          title: 'Zabroniony',
          message: 'Dostęp do żądanej strony jest zabroniony.',
        },
        404: {
          code: '404',
          title: 'Nie znaleziono strony',
          message: 'Przykro nam, ale strona, której szukasz, nie istnieje.',
        },
        419: {
          code: '419',
          title: 'Strona wygasła',
          message: 'Ta strona wygasła. Zaloguj się ponownie.',
        },
        429: {
          code: '429',
          title: 'Przekroczono limit zapytań',
          message: 'Wysłano ostatnio zbyt wiele zapytań. Poczekaj i spróbuj ponownie później.',
        },
        500: {
          code: '500',
          title: 'Błąd serwera',
          message: 'Wystąpił wewnętrzny błąd serwera.',
        },
        503: {
          code: '503',
          title: 'Serwis niedostępny',
          message: 'Serwer jest tymczasowo niedostępny. Spróbuj ponownie później.',
        },
      }[this.status]
    },

  },
}
</script>
