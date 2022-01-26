<template>
  <InertiaHead title="Twoje wnioski urlopowe" />
  <div class="bg-white sm:rounded-lg shadow-md">
    <div class="flex justify-between items-center p-4 sm:px-6">
      <div>
        <h2 class="text-lg leading-6 font-medium text-gray-900">
          Twoje wnioski urlopowe
        </h2>
      </div>
      <div>
        <InertiaLink
          href="vacation-requests/create"
          class="inline-flex items-center px-4 py-3 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-blumilk-600 hover:bg-blumilk-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blumilk-500"
        >
          Dodaj wniosek
        </InertiaLink>
      </div>
    </div>
    <div class="overflow-x-auto xl:overflow-x-visible overflow-y-auto xl:overflow-y-visible">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th
              scope="col"
              class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider"
            >
              Numer
            </th>
            <th
              scope="col"
              class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider"
            >
              Rodzaj urlopu
            </th>
            <th
              scope="col"
              class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider"
            >
              Status
            </th>
            <th
              scope="col"
              class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider"
            >
              Od
            </th>
            <th
              scope="col"
              class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider"
            >
              Do
            </th>
            <th
              scope="col"
              class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider"
            >
              Dni urlopu
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-100">
          <tr
            v-for="request in requests.data"
            :key="request.id"
            class="hover:bg-blumilk-25"
          >
            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
              <a
                :href="`/vacation-requests/${request.id}`"
                class="font-semibold text-blumilk-600 hover:text-blumilk-500 hover:underline"
              >
                {{ request.name }}
              </a>
            </td>
            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ request.type }}
            </td>
            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ request.state }}
            </td>
            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ request.from }}
            </td>
            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ request.to }}
            </td>
            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
              X
            </td>
          </tr>
          <tr
            v-if="! requests.data.length"
          >
            <td
              colspan="100%"
              class="text-center py-4 text-xl leading-5 text-gray-700"
            >
              Brak danych
            </td>
          </tr>
        </tbody>
      </table>
      <div
        v-if="requests.data.length && requests.meta.last_page !== 1"
        class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6 rounded-b-lg"
      >
        <div class="flex-1 flex justify-between sm:hidden">
          <InertiaLink
            :is="requests.links.prev ? 'InertiaLink': 'span'"
            :href="requests.links.prev"
            class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
          >
            Poprzednia
          </InertiaLink>
          <Component
            :is="requests.links.next ? 'InertiaLink': 'span'"
            :href="requests.links.next"
            class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
          >
            Następna
          </Component>
        </div>
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
          <div class="text-sm text-gray-700">
            Wyświetlanie
            <span class="font-medium">{{ requests.meta.from }}</span>
            od
            <span class="font-medium">{{ requests.meta.to }}</span>
            do
            <span class="font-medium">{{ requests.meta.total }}</span>
            wyników
          </div>
          <nav class="relative z-0 inline-flex space-x-1">
            <template
              v-for="(link, index) in requests.meta.links"
              :key="index"
            >
              <Component
                :is="link.url ? 'InertiaLink' : 'span'"
                :href="link.url"
                :preserve-scroll="true"
                class="relative inline-flex items-center px-4 py-2 border rounded-md text-sm font-medium"
                :class="{ 'z-10 bg-blumilk-25 border-blumilk-500 text-blumilk-600': link.active, 'bg-white border-gray-300 text-gray-500': !link.active, 'hover:bg-blumilk-25': link.url, 'border-none': !link.url}"
                v-text="link.label"
              />
            </template>
          </nav>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import {DotsVerticalIcon, PencilIcon, TrashIcon} from '@heroicons/vue/solid'

export default {
  name: 'VacationRequestIndex',
  components: {
    DotsVerticalIcon,
    PencilIcon,
    TrashIcon,
  },
  props: {
    requests: {
      type: Object,
      default: () => null,
    },
  },
}
</script>
