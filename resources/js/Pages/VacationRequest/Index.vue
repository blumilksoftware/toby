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
          href="/vacation-requests/create"
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
          :class="[status.value === filters.status ? 'text-gray-900' : '', 'text-gray-500 hover:text-gray-700 group relative min-w-0 flex-1 overflow-hidden bg-white py-4 px-4 text-sm font-medium text-center hover:bg-gray-50 focus:z-10']"
        >
          <span>{{ status.name }}</span>
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
                :href="`/vacation-requests/${request.id}`"
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
                :href="`/vacation-requests/${request.id}`"
                class="flex justify-around"
              >
                <ChevronRightIcon class="block w-6 h-6 fill-blumilk-500" />
              </InertiaLink>
              <InertiaLink
                :href="`/vacation-requests/${request.id}`"
                class="absolute inset-0"
              />
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
import {
  ChevronRightIcon,
  ClockIcon,
  DotsVerticalIcon,
  PencilIcon,
  ThumbDownIcon,
  ThumbUpIcon,
  TrashIcon,
  XIcon,
  CheckIcon,
  DocumentTextIcon,
} from '@heroicons/vue/solid'
import Status from '@/Shared/Status'
import VacationType from '@/Shared/VacationType'

export default {
  name: 'VacationRequestIndex',
  components: {
    DotsVerticalIcon,
    PencilIcon,
    TrashIcon,
    ChevronRightIcon,
    ThumbUpIcon,
    ClockIcon,
    XIcon,
    CheckIcon,
    DocumentTextIcon,
    ThumbDownIcon,
    Status,
    VacationType,
  },
  props: {
    requests: {
      type: Object,
      default: () => null,
    },
    filters: {
      type: Object,
      default: () => null,
    },
  },
  setup() {
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

    return {
      statuses,
    }
  },
}
</script>
