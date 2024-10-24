<script setup>
import Status from '@/Shared/Status.vue'
import VacationType from '@/Shared/VacationType.vue'
import EmptyState from '@/Shared/Feedbacks/EmptyState.vue'
import { RectangleStackIcon } from '@heroicons/vue/24/solid'
import InertiaLink from '@/Shared/InertiaLink.vue'

defineProps({
  requests: Object,
  profilePage: {
    type: Boolean,
    default: false,
  },
  label: {
    type: String,
    default: 'Moje wnioski',
  },
})

</script>

<template>
  <section class="bg-white shadow-md">
    <div class="p-4 sm:px-6">
      <h2 class="text-lg font-medium leading-6 text-gray-900">
        {{ label }}
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
                <div class="flex">
                  <div
                    v-if="request.days.length > 1"
                    class="ml-2 text-sm text-gray-600"
                  >
                    {{ request.from }} - {{ request.to }}
                  </div>
                  <div
                    v-else
                    class="ml-2 text-sm text-gray-600"
                  >
                    {{ request.from }}
                  </div>
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
              <EmptyState>
                <template #head>
                  <RectangleStackIcon class="mx-auto size-12" />
                </template>
                <template #title>
                  Brak wniosków
                </template>
                <template #text>
                  Nie utworzono jeszcze wniosku
                </template>
              </EmptyState>
            </p>
          </li>
        </ul>
      </div>
      <div
        v-if="requests.length"
        class="mt-6"
      >
        <InertiaLink
          v-if="!profilePage"
          href="/vacation/requests/me"
          class="flex justify-center items-center py-2 px-4 w-full text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blumilk-500 shadow-sm"
        >
          Zobacz wszystkie
        </InertiaLink>
        <InertiaLink
          v-else
          :href="`/vacation/requests?user=${requests[0].user.id}`"
          class="flex justify-center items-center py-2 px-4 w-full text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blumilk-500 shadow-sm"
        >
          Zobacz wszystkie
        </InertiaLink>
      </div>
    </div>
  </section>
</template>
