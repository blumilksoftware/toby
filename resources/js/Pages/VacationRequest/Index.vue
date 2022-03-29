<template>
  <InertiaHead title="Moje wnioski urlopowe" />
  <div class="bg-white shadow-md">
    <div class="flex justify-between items-center p-4 sm:px-6">
      <div>
        <h2 class="text-lg leading-6 font-medium text-gray-900">
          Moje wnioski urlopowe
        </h2>
      </div>
      <div>
        <InertiaLink
          href="/vacation/requests/create"
          class="inline-flex items-center px-4 py-3 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-blumilk-600 hover:bg-blumilk-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blumilk-500"
        >
          Dodaj wniosek
        </InertiaLink>
      </div>
    </div>
    <div class="overflow-x-auto xl:overflow-x-visible overflow-y-auto xl:overflow-y-visible">
      <nav class="relative shadow flex divide-x divide-gray-200 border-t border-gray-200">
        <InertiaLink
          v-for="(status, index) in statuses"
          :key="index"
          :data="{ status: status.value }"
          :class="[status.value === filters.status ? 'text-blumilk-600 font-semibold' : 'hover:bg-blumilk-25 text-gray-700 focus:z-10', 'group relative min-w-0 flex-1 overflow-hidden bg-white py-4 px-4 text-sm font-medium text-center']"
        >
          <span>{{ status.name }}</span>
          <span
            v-if="stats[status.value]"
            :class="[status.value === filters.status ? 'bg-blumilk-50 text-blumilk-600' : 'bg-gray-100 text-gray-600', 'hidden ml-3 py-0.5 px-2.5 rounded-full text-xs font-semibold 2xl:inline-block']"
          >
            {{ stats[status.value] }}
          </span>
          <span :class="[status.value === filters.status ? 'bg-blumilk-500' : 'bg-transparent', 'absolute inset-x-0 bottom-0 h-0.5']" />
        </InertiaLink>
      </nav>
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th
              scope="col"
              class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider"
            >
              Numer
            </th>
            <th
              scope="col"
              class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider"
            >
              Rodzaj urlopu
            </th>
            <th
              scope="col"
              class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider"
            >
              Od
            </th>
            <th
              scope="col"
              class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider"
            >
              Do
            </th>
            <th
              scope="col"
              class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider"
            >
              Dni urlopu
            </th>
            <th
              scope="col"
              class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider"
            >
              Status
            </th>
            <th scope="col" />
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-100">
          <tr
            v-for="request in requests.data"
            :key="request.id"
            class="hover:bg-blumilk-25 relative"
          >
            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
              <InertiaLink
                :href="`/vacation/requests/${request.id}`"
                class="font-semibold text-blumilk-600 hover:text-blumilk-500 hover:underline"
              >
                {{ request.name }}
              </InertiaLink>
            </td>
            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-500">
              <VacationType :type="request.type" />
            </td>
            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ request.from }}
            </td>
            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ request.to }}
            </td>
            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ request.days.length }}
            </td>
            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
              <Status :status="request.state" />
            </td>
            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
              <InertiaLink
                :href="`/vacation/requests/${request.id}`"
                class="flex justify-around"
              >
                <ChevronRightIcon class="block w-6 h-6 fill-blumilk-500" />
              </InertiaLink>
              <InertiaLink
                :href="`/vacation/requests/${request.id}`"
                class="absolute inset-0"
              />
            </td>
          </tr>
          <tr v-if="! requests.data.length">
            <td
              colspan="100%"
              class="text-center py-4 text-xl leading-5 text-gray-700"
            >
              Brak danych
            </td>
          </tr>
        </tbody>
      </table>
      <Pagination :pagination="requests.meta" />
    </div>
  </div>
</template>

<script setup>
import { ChevronRightIcon } from '@heroicons/vue/solid'
import Status from '@/Shared/Status'
import VacationType from '@/Shared/VacationType'
import Pagination from '@/Shared/Pagination'

defineProps({
  requests: Object,
  stats: Object,
  filters: Object,
})

const statuses = [
  {
    name: 'Wszystkie',
    value: 'all',
  },
  {
    name: 'W trakcie',
    value: 'pending',
  },
  {
    name: 'Zatwierdzone',
    value: 'success',
  },
  {
    name: 'Odrzucone/anulowane',
    value: 'failed',
  },
]
</script>
