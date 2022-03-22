<template>
  <section class="bg-white shadow-md">
    <div class="p-4 sm:px-6">
      <h2 class="text-lg leading-6 font-medium text-gray-900">
        Twoje wnioski
      </h2>
    </div>
    <div class="border-t border-gray-200 pb-5 px-4 sm:px-6">
      <div class="flow-root mt-6">
        <ul class="-my-5 divide-y divide-gray-200">
          <li
            v-for="request in requests"
            :key="request.id"
            class="py-5"
          >
            <div class="relative focus-within:ring-2 focus-within:ring-blumilk-500">
              <h3 class="text-sm font-semibold text-blumilk-600 hover:text-blumilk-500">
                <InertiaLink
                  :href="`/vacation-requests/${request.id}`"
                  class="hover:underline focus:outline-none"
                >
                  <span class="absolute inset-0" />
                  Wniosek o {{ findType(request.type).text.toLowerCase() }}
                  [{{ request.name }}]
                </InertiaLink>
              </h3>
              <p class="mt-1 text-sm text-gray-600">
                {{ request.from }} - {{ request.to }}
              </p>
              <p class="mt-2 text-sm text-gray-600">
                <Status :status="request.state" />
              </p>
            </div>
          </li>
          <li v-if="! requests.length">
            <p class="py-2">
              Brak danych
            </p>
          </li>
        </ul>
      </div>
      <div class="mt-6">
        <InertiaLink
          href="/vacation-requests/me"
          class="w-full flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blumilk-500"
        >
          Zobacz wszystkie
        </InertiaLink>
      </div>
    </div>
  </section>
</template>

<script setup>
import useVacationTypeInfo from '@/Composables/vacationTypeInfo'

defineProps({
  requests: Object,
})

const { findType } = useVacationTypeInfo()
</script>
