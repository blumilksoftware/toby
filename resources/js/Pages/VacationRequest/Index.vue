<template>
  <InertiaHead title="Moje wnioski urlopowe" />
  <div class="bg-white shadow-md">
    <div class="flex justify-between items-center p-4 sm:px-6">
      <h2 class="text-lg leading-6 font-medium text-gray-900">
        Moje wnioski urlopowe
      </h2>
      <div>
        <InertiaLink
          href="/vacation/requests/create"
          class="inline-flex items-center px-4 py-3 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-blumilk-600 hover:bg-blumilk-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blumilk-500"
        >
          Dodaj wniosek
        </InertiaLink>
      </div>
    </div>
    <div class="border-t border-gray-200">
      <div class="hidden md:flex relative shadow divide-x divide-gray-200">
        <button
          v-for="(status, index) in statuses"
          :key="index"
          :class="[status.value === filters.status ? 'text-blumilk-600 font-semibold' : 'hover:bg-blumilk-25 text-gray-700 focus:z-10', 'group relative min-w-0 flex-1 overflow-hidden bg-white py-4 px-4 text-sm font-medium text-center']"
          @click="form.status = status"
        >
          <span>{{ status.name }}</span>
          <span
            v-if="stats[status.value]"
            :class="[status.value === filters.status ? 'bg-blumilk-50 text-blumilk-600' : 'bg-gray-100 text-gray-600', 'hidden ml-3 py-0.5 px-2.5 rounded-full text-xs font-semibold 2xl:inline-block']"
          >
            {{ stats[status.value] }}
          </span>
          <span
            :class="[status.value === filters.status ? 'bg-blumilk-500' : 'bg-transparent', 'absolute inset-x-0 bottom-0 h-0.5']"
          />
        </button>
      </div>
      <div class="md:hidden px-4 py-4 grid grid-cols-1 md:grid-cols-2 gap-2 md:gap-4">
        <Listbox
          v-model="form.status"
          as="div"
        >
          <ListboxLabel class="block text-sm font-medium text-gray-700 mb-2">
            Status
          </ListboxLabel>
          <div class="mt-1 relative sm:mt-0">
            <ListboxButton
              class="bg-white relative w-full h-10 max-w-lg border rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-default sm:text-sm focus:outline-none focus:ring-1 focus:ring-blumilk-500 focus:border-blumilk-500 sm:text-sm border-gray-300"
            >
              <span class="flex items-center">
                {{ form.status.name }}
              </span>
              <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                <SelectorIcon class="h-5 w-5 text-gray-400" />
              </span>
            </ListboxButton>

            <transition
              leave-active-class="transition ease-in duration-100"
              leave-from-class="opacity-100"
              leave-to-class="opacity-0"
            >
              <ListboxOptions
                class="absolute z-10 mt-1 w-full max-w-lg bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm"
              >
                <ListboxOption
                  v-for="status in statuses"
                  :key="status.value"
                  v-slot="{ active, selected }"
                  as="template"
                  :value="status"
                >
                  <li
                    :class="[active ? 'text-white bg-blumilk-600' : 'text-gray-900', 'cursor-default truncate select-none relative py-2 pl-3 pr-9']"
                  >
                    {{ status.name }}

                    <span
                      v-if="selected"
                      :class="[active ? 'text-white' : 'text-blumilk-600', 'absolute inset-y-0 right-0 flex items-center pr-4']"
                    >
                      <CheckIcon class="h-5 w-5" />
                    </span>
                  </li>
                </ListboxOption>
              </ListboxOptions>
            </transition>
          </div>
        </Listbox>
      </div>
    </div>
    <div class="overflow-x-auto xl:overflow-x-visible overflow-y-auto xl:overflow-y-visible">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th
              scope="col"
              class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap"
            >
              Numer
            </th>
            <th
              scope="col"
              class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap"
            >
              Rodzaj urlopu
            </th>
            <th
              scope="col"
              class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap"
            >
              Od
            </th>
            <th
              scope="col"
              class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap"
            >
              Do
            </th>
            <th
              scope="col"
              class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap"
            >
              Dni urlopu
            </th>
            <th
              scope="col"
              class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap"
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
import { ChevronRightIcon, SelectorIcon, CheckIcon } from '@heroicons/vue/solid'
import Status from '@/Shared/Status'
import VacationType from '@/Shared/VacationType'
import Pagination from '@/Shared/Pagination'
import { Listbox, ListboxButton, ListboxLabel, ListboxOption, ListboxOptions } from '@headlessui/vue'
import { reactive, watch } from 'vue'
import { debounce } from 'lodash'
import { Inertia } from '@inertiajs/inertia'

const props = defineProps({
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

const form = reactive({
  status: statuses.find(status => status.value === props.filters.status) ?? statuses[0],
})

watch(form, debounce(() => {
  Inertia.get('/vacation/requests/me', { status: form.status.value }, {
    preserveState: true,
    replace: false,
  })
}, 300))

</script>
