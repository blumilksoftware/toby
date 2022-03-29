<template>
  <InertiaHead title="Wnioski urlopowe" />
  <div class="bg-white shadow-md">
    <div class="flex justify-between items-center p-4 sm:px-6">
      <div>
        <h2 class="text-lg leading-6 font-medium text-gray-900">
          Wnioski urlopowe
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
      <div class="border-t border-gray-200">
        <div class="px-4 grid grid-cols-2 gap-4">
          <div>
            <Listbox
              v-model="form.user"
              as="div"
              class="py-4 items-center"
            >
              <ListboxLabel class="block text-sm font-medium text-gray-700 mb-2">
                Pracownik
              </ListboxLabel>
              <div class="mt-1 relative sm:mt-0">
                <ListboxButton
                  class="bg-white relative w-full max-w-lg border rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-default sm:text-sm focus:ring-1 focus:ring-blumilk-500 focus:border-blumilk-500 sm:text-sm border-gray-300"
                >
                  <span v-if="form.user === null">
                    Wszyscy
                  </span>
                  <span
                    v-else
                    class="flex items-center"
                  >
                    <img
                      :src="form.user.avatar"
                      class="flex-shrink-0 h-6 w-6 rounded-full"
                    >
                    <span class="ml-3 block truncate">{{ form.user.name }}</span>
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
                      v-slot="{ active }"
                      as="template"
                      :value="null"
                    >
                      <li
                        :class="[active ? 'text-white bg-blumilk-600' : 'text-gray-900', 'cursor-default select-none relative py-2 pl-3 pr-9']"
                      >
                        <div class="flex items-center">
                          Wszyscy pracownicy
                        </div>

                        <span
                          v-if="form.user === null"
                          :class="[active ? 'text-white' : 'text-blumilk-600', 'absolute inset-y-0 right-0 flex items-center pr-4']"
                        >
                          <CheckIcon class="h-5 w-5" />
                        </span>
                      </li>
                    </ListboxOption>
                    <ListboxOption
                      v-for="user in users.data"
                      :key="user.id"
                      v-slot="{ active }"
                      as="template"
                      :value="user"
                    >
                      <li
                        :class="[active ? 'text-white bg-blumilk-600' : 'text-gray-900', 'cursor-default select-none relative py-2 pl-3 pr-9']"
                      >
                        <div class="flex items-center">
                          <img
                            :src="user.avatar"
                            class="flex-shrink-0 h-6 w-6 rounded-full"
                          >
                          <span
                            :class="[form.user?.id === user.id ? 'font-semibold' : 'font-normal', 'ml-3 block truncate']"
                          >
                            {{ user.name }}
                          </span>
                        </div>
                        <span
                          v-if="form.user?.id === user.id"
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
          <div>
            <Listbox
              v-model="form.status"
              as="div"
              class="py-4 items-center"
            >
              <ListboxLabel class="block text-sm font-medium text-gray-700 mb-2">
                Status
              </ListboxLabel>
              <div class="mt-1 relative sm:mt-0">
                <ListboxButton
                  class="bg-white relative w-full max-w-lg border rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-default sm:text-sm focus:ring-1 focus:ring-blumilk-500 focus:border-blumilk-500 sm:text-sm border-gray-300"
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
                        :class="[active ? 'text-white bg-blumilk-600' : 'text-gray-900', 'cursor-default select-none relative py-2 pl-3 pr-9']"
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
      </div>
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
              Pracownik
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
              <div class="flex">
                <img
                  class="h-10 w-10 rounded-full"
                  :src="request.user.avatar"
                >
                <div class="ml-3">
                  <p class="text-sm font-medium text-gray-900">
                    {{ request.user.name }}
                  </p>
                  <p class="text-sm text-gray-500">
                    {{ request.user.email }}
                  </p>
                </div>
              </div>
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
import { CheckIcon, ChevronRightIcon, SelectorIcon } from '@heroicons/vue/solid'
import Status from '@/Shared/Status'
import VacationType from '@/Shared/VacationType'
import { watch, reactive } from 'vue'
import { debounce } from 'lodash'
import { Inertia } from '@inertiajs/inertia'
import { Listbox, ListboxButton, ListboxLabel, ListboxOption, ListboxOptions } from '@headlessui/vue'
import Pagination from '@/Shared/Pagination'

const props = defineProps({
  requests: Object,
  users: Object,
  filters: Object,
})

const statuses = [
  {
    name: 'Wszystkie',
    value: 'all',
  },
  {
    name: 'Oczekujące na akcje',
    value: 'waiting_for_action',
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
  user: props.users.data.find(user => user.id === props.filters.user) ?? null,
  status: statuses.find(status => status.value === props.filters.status) ?? statuses[0],
})

watch(form, debounce(() => {
  Inertia.get('/vacation/requests', { user: form.user?.id, status: form.status.value }, {
    preserveState: true,
    replace: true,
  })
}, 300))
</script>
