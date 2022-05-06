<template>
  <section class="bg-white shadow-md">
    <div class="p-4 sm:px-6">
      <h2 class="text-lg font-medium leading-6 text-gray-900">
        Twoje wnioski
      </h2>
    </div>
    <div class="px-4 pb-5 border-t border-gray-200 sm:px-6">
      <div class="flow-root mt-6">
        <ul class="-my-5 divide-y divide-gray-200">
          <li
            v-for="request in requests"
            :key="request.id"
            class="py-5"
          >
            <div class="relative focus-within:ring-2 focus-within:ring-blumilk-500">
              <div class="flex flex-row">
                <h3 class="text-sm font-semibold text-blumilk-600 hover:text-blumilk-500">
                  <InertiaLink
                    :href="`/vacation/requests/${request.id}`"
                    class="hover:underline focus:outline-none"
                  >
                    <span class="absolute inset-0" />
                    Wniosek [{{ request.name }}]
                  </InertiaLink>
                </h3>
                <div class="ml-2 text-sm text-gray-600">
                  {{ request.from }} - {{ request.to }}
                </div>
              </div>
              <p class="mt-2 text-sm text-gray-600">
                <VacationType :type="request.type" />
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
          href="/vacation/requests/me"
          class="flex justify-center items-center py-2 px-4 w-full text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blumilk-500 shadow-sm"
        >
          Zobacz wszystkie
        </InertiaLink>
      </div>
    </div>
  </section>
</template>

<script setup>
import Status from '@/Shared/Status'
import VacationType from '@/Shared/VacationType'

defineProps({
  requests: Object,
})

</script>
