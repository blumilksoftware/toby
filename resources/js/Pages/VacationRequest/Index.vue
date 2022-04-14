<template>
  <InertiaHead title="Moje wnioski" />
  <div class="bg-white shadow-md">
    <div class="flex justify-between items-center p-4 sm:px-6">
      <h2 class="text-lg font-medium leading-6 text-gray-900">
        Moje wnioski
      </h2>
      <div>
        <InertiaLink
          href="/vacation/requests/create"
          class="inline-flex items-center py-3 px-4 text-sm font-medium leading-4 text-white bg-blumilk-600 hover:bg-blumilk-700 rounded-md border border-transparent focus:outline-none focus:ring-2 focus:ring-blumilk-500 focus:ring-offset-2 shadow-sm"
        >
          Dodaj wniosek
        </InertiaLink>
      </div>
    </div>
    <div class="border-t border-gray-200">
      <div class="hidden relative divide-x divide-gray-200 shadow md:flex">
        <button
          v-for="(status, index) in statuses"
          :key="index"
          :class="[status.value === filters.status ? 'text-blumilk-600 font-semibold' : 'hover:bg-blumilk-25 text-gray-700 focus:z-10', 'group relative min-w-0 flex-1 overflow-hidden focus:outline-blumilk-500 bg-white py-4 px-4 text-sm font-medium text-center']"
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
      <div class="grid grid-cols-1 gap-2 p-4 md:hidden md:grid-cols-2 md:gap-4">
        <Listbox
          v-model="form.status"
          as="div"
        >
          <ListboxLabel class="block mb-2 text-sm font-medium text-gray-700">
            Status
          </ListboxLabel>
          <div class="relative mt-1 sm:mt-0">
            <ListboxButton
              class="relative py-2 pr-10 pl-3 w-full max-w-lg h-10 text-left bg-white rounded-md border border-gray-300 focus:border-blumilk-500 focus:outline-none focus:ring-1 focus:ring-blumilk-500 shadow-sm cursor-default sm:text-sm"
            >
              <span class="flex items-center">
                {{ form.status.name }}
              </span>
              <span class="flex absolute inset-y-0 right-0 items-center pr-2 pointer-events-none">
                <SelectorIcon class="w-5 h-5 text-gray-400" />
              </span>
            </ListboxButton>

            <transition
              leave-active-class="transition ease-in duration-100"
              leave-from-class="opacity-100"
              leave-to-class="opacity-0"
            >
              <ListboxOptions
                class="overflow-auto absolute z-10 py-1 mt-1 w-full max-w-lg max-h-60 text-base bg-white rounded-md focus:outline-none ring-1 ring-black ring-opacity-5 shadow-lg sm:text-sm"
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
                      <CheckIcon class="w-5 h-5" />
                    </span>
                  </li>
                </ListboxOption>
              </ListboxOptions>
            </transition>
          </div>
        </Listbox>
      </div>
    </div>
    <div class="overflow-auto xl:overflow-visible">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th
              scope="col"
              class="py-3 px-4 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase whitespace-nowrap"
            >
              Numer
            </th>
            <th
              scope="col"
              class="py-3 px-4 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase whitespace-nowrap"
            >
              Rodzaj wniosku
            </th>
            <th
              scope="col"
              class="py-3 px-4 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase whitespace-nowrap"
            >
              Od
            </th>
            <th
              scope="col"
              class="py-3 px-4 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase whitespace-nowrap"
            >
              Do
            </th>
            <th
              scope="col"
              class="py-3 px-4 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase whitespace-nowrap"
            >
              Dni urlopu
            </th>
            <th
              scope="col"
              class="py-3 px-4 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase whitespace-nowrap"
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
            class="relative hover:bg-blumilk-25"
          >
            <td class="p-4 text-sm text-gray-500 whitespace-nowrap">
              <InertiaLink
                :href="`/vacation/requests/${request.id}`"
                class="font-semibold text-blumilk-600 hover:text-blumilk-500 hover:underline focus:outline-blumilk-500"
              >
                {{ request.name }}
              </InertiaLink>
            </td>
            <td class="p-4 text-sm font-medium text-gray-500 whitespace-nowrap">
              <VacationType :type="request.type" />
            </td>
            <td class="p-4 text-sm text-gray-500 whitespace-nowrap">
              {{ request.from }}
            </td>
            <td class="p-4 text-sm text-gray-500 whitespace-nowrap">
              {{ request.to }}
            </td>
            <td class="p-4 text-sm text-gray-500 whitespace-nowrap">
              {{ request.days.length }}
            </td>
            <td class="p-4 text-sm text-gray-500 whitespace-nowrap">
              <Status :status="request.state" />
            </td>
            <td class="p-4 text-sm text-gray-500 whitespace-nowrap">
              <InertiaLink
                :href="`/vacation/requests/${request.id}`"
                class="flex justify-around focus:outline-blumilk-500"
              >
                <ChevronRightIcon class="block w-6 h-6 fill-blumilk-500" />
              </InertiaLink>
              <InertiaLink
                :href="`/vacation/requests/${request.id}`"
                class="absolute inset-0 focus:outline-blumilk-500"
              />
            </td>
          </tr>
          <tr v-if="! requests.data.length">
            <td
              colspan="100%"
              class="py-4 text-xl leading-5 text-center text-gray-700"
            >
              Brak danych
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <Pagination :pagination="requests.meta" />
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
