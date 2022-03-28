<template>
  <InertiaHead title="xD" />
  <div class="min-h-full px-4 py-16 sm:px-6 sm:py-24 md:grid md:place-items-center lg:px-8">
    <div class="max-w-max mx-auto">
      <main class="sm:flex">
        <div>
          <div>
            <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight sm:text-5xl">
              Błąd
            </h1>
            <p class="mt-1 text-base text-gray-500">
              Wystąpił nieoczekiwany błąd. Administrator został o tym powiadomiony.
            </p>
          </div>
          <div class="mt-10 flex space-x-3 sm:border-l sm:border-transparent sm:pl-6">
            <InertiaLink
              href="/"
              class="inline-flex items-center px-4 py-3 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-blumilk-600 hover:bg-blumilk-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blumilk-500"
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
import GuestLayout from '@/Shared/Layout/GuestLayout'

export default {
  name: 'DefaultErrorPage',
  layout: GuestLayout,
  // props: {
  //   status: Number,
  // },
  // computed: {
  //   error() {
  //     return {
  //       401: {
  //         code: '401',
  //         title: 'Nieuprawniony dostęp',
  //         message: 'Aby mieć dostęp do tej strony, musisz się zalogować.',
  //       },
  //       403: {
  //         code: '403',
  //         title: 'Zabroniony',
  //         message: 'Dostęp do żądanej strony jest zabroniony.',
  //       },
  //       404: {
  //         code: '404',
  //         title: 'Nie znaleziono strony',
  //         message: 'Przykro nam, ale strona, której szukasz, nie istnieje.',
  //       },
  //       419: {
  //         code: '419',
  //         title: 'Strona wygasła',
  //         message: 'Ta strona wygasła. Zaloguj się ponownie.',
  //       },
  //       500: {
  //         code: '500',
  //         title: 'Błąd serwera',
  //         message: 'Wystąpił wewnętrzny błąd serwera.',
  //       },
  //       503: {
  //         code: '503',
  //         title: 'Serwis niedostępny',
  //         message: 'Serwer jest tymczasowo niedostępny. Spróbuj ponownie później.',
  //       },
  //     }[this.status]
  //   },
  //
  // },
}
</script>