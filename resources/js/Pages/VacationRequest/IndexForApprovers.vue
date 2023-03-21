<script setup>
import { CheckIcon, ChevronRightIcon, ChevronUpDownIcon } from '@heroicons/vue/24/solid'
import Status from '@/Shared/Status.vue'
import VacationType from '@/Shared/VacationType.vue'
import { watch, reactive } from 'vue'
import { debounce } from 'lodash'
import { Inertia } from '@inertiajs/inertia'
import { Listbox, ListboxButton, ListboxLabel, ListboxOption, ListboxOptions } from '@headlessui/vue'
import Pagination from '@/Shared/Pagination.vue'
import EmptyState from '@/Shared/Feedbacks/EmptyState.vue'

const props = defineProps({
  requests: Object,
  users: Object,
  filters: Object,
  types: Object,
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
  type: props.types.find(type => type.value === props.filters.type) ?? null,
})

watch(form, debounce(() => {
  Inertia.get('/vacation/requests', {
    user: form.user?.id,
    status: form.status.value,
    type: form.type?.value,
  }, {
    preserveState: true,
    replace: true,
  })
}, 150))
</script>

<template>
  <InertiaHead title="Wnioski" />
  <div class="bg-white shadow-md">
    <div class="flex justify-between items-center p-4 sm:px-6">
      <div>
        <h2 class="text-lg font-medium leading-6 text-gray-900">
          Lista wniosków
        </h2>
      </div>
      <div>
        <InertiaLink
          href="/vacation/requests/create"
          class="inline-flex items-center py-3 px-4 text-sm font-medium leading-4 text-white bg-blumilk-600 hover:bg-blumilk-700 rounded-md border border-transparent focus:outline-none focus:ring-2 focus:ring-blumilk-500 focus:ring-offset-2 shadow-sm"
          dusk="create-vacation-request-button"
        >
          Dodaj wniosek
        </InertiaLink>
      </div>
    </div>
    <div class="border-t border-gray-200">
      <div class="grid grid-cols-1 gap-2 p-4 md:grid-cols-3 md:gap-4">
        <Listbox
          v-model="form.user"
          as="div"
        >
          <ListboxLabel class="block mb-2 text-sm font-medium text-gray-700">
            Pracownik
          </ListboxLabel>
          <div class="relative mt-1 sm:mt-0">
            <ListboxButton
              class="relative py-2 pr-10 pl-3 w-full max-w-lg h-10 text-left bg-white rounded-md border border-gray-300 focus:border-blumilk-500 focus:outline-none focus:ring-1 focus:ring-blumilk-500 shadow-sm cursor-default sm:text-sm"
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
                  class="shrink-0 w-6 h-6 rounded-full"
                >
                <span class="block ml-3 truncate">{{ form.user.name }}</span>
              </span>
              <span class="flex absolute inset-y-0 right-0 items-center pr-2 pointer-events-none">
                <ChevronUpDownIcon class="w-5 h-5 text-gray-400" />
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
                  v-slot="{ active }"
                  as="template"
                  :value="null"
                >
                  <li
                    :class="[active ? 'bg-gray-100' : 'text-gray-900', 'cursor-default select-none relative py-2 pl-3 pr-9']"
                  >
                    <div class="flex items-center">
                      Wszyscy
                    </div>

                    <span
                      v-if="form.user === null"
                      :class="['text-blumilk-600 absolute inset-y-0 right-0 flex items-center pr-4']"
                    >
                      <CheckIcon class="w-5 h-5" />
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
                    :class="[active ? 'bg-gray-100' : 'text-gray-900', 'cursor-default select-none relative py-2 pl-3 pr-9']"
                  >
                    <div class="flex items-center">
                      <img
                        :src="user.avatar"
                        class="shrink-0 w-6 h-6 rounded-full"
                      >
                      <span
                        :class="[form.user?.id === user.id ? 'font-semibold' : 'font-normal', 'ml-3 block truncate']"
                      >
                        {{ user.name }}
                      </span>
                    </div>
                    <span
                      v-if="form.user?.id === user.id"
                      :class="['text-blumilk-600 absolute inset-y-0 right-0 flex items-center pr-4']"
                    >
                      <CheckIcon class="w-5 h-5" />
                    </span>
                  </li>
                </ListboxOption>
              </ListboxOptions>
            </transition>
          </div>
        </Listbox>
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
                <ChevronUpDownIcon class="w-5 h-5 text-gray-400" />
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
                    :class="[active ? 'bg-gray-100' : 'text-gray-900', 'cursor-default truncate select-none relative py-2 pl-3 pr-9']"
                  >
                    {{ status.name }}

                    <span
                      v-if="selected"
                      :class="['text-blumilk-600 absolute inset-y-0 right-0 flex items-center pr-4']"
                    >
                      <CheckIcon class="w-5 h-5" />
                    </span>
                  </li>
                </ListboxOption>
              </ListboxOptions>
            </transition>
          </div>
        </Listbox>
        <Listbox
          v-model="form.type"
          as="div"
        >
          <ListboxLabel class="block mb-2 text-sm font-medium text-gray-700">
            Rodzaj wniosku
          </ListboxLabel>
          <div class="relative mt-1 sm:mt-0">
            <ListboxButton
              class="relative py-2 pr-10 pl-3 w-full max-w-lg h-10 text-left bg-white rounded-md border border-gray-300 focus:border-blumilk-500 focus:outline-none focus:ring-1 focus:ring-blumilk-500 shadow-sm cursor-default sm:text-sm"
            >
              <VacationType
                v-if="form.type"
                :type="form.type.value"
              />

              <span
                v-else
                class="flex items-center"
              >
                Wszystkie
              </span>
              <span class="flex absolute inset-y-0 right-0 items-center pr-2 pointer-events-none">
                <ChevronUpDownIcon class="w-5 h-5 text-gray-400" />
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
                  v-slot="{ active, selected }"
                  as="template"
                  :value="null"
                >
                  <li
                    :class="[active ? 'bg-gray-100' : 'text-gray-900', 'cursor-default truncate select-none relative py-2 pl-3 pr-9']"
                  >
                    Wszystkie

                    <span
                      v-if="selected"
                      :class="['text-blumilk-600 absolute inset-y-0 right-0 flex items-center pr-4']"
                    >
                      <CheckIcon class="w-5 h-5" />
                    </span>
                  </li>
                </ListboxOption>
                <ListboxOption
                  v-for="type in types"
                  :key="type.value"
                  v-slot="{ active }"
                  as="template"
                  :value="type"
                >
                  <li
                    :class="[active ? 'bg-gray-100' : 'text-gray-900', 'cursor-default truncate select-none relative py-2 pl-3 pr-9']"
                  >
                    <VacationType :type="type.value" />
                    <span
                      v-if="form.type?.value === type.value"
                      :class="['text-blumilk-600 absolute inset-y-0 right-0 flex items-center pr-4']"
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
              Pracownik
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
          <InertiaLink
            v-for="request in requests.data"
            :key="request.id"
            as="tr"
            :href="`/vacation/requests/${request.id}`"
            class="relative hover:bg-blumilk-25 hover:cursor-pointer"
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
              <div class="flex">
                <div class="w-10 h-10 rounded-full">
                  <img :src="request.user.avatar">
                </div>
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
            </td>
          </InertiaLink>
          <tr v-if="! requests.data.length">
            <td
              colspan="100%"
              class="py-4 text-xl leading-5 text-center text-gray-700"
            >
              <EmptyState>
                <template #title>
                  Brak wniosków
                </template>
                <template #text>
                  Spróbuj sformułować zapytanie inaczej
                </template>
              </EmptyState>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <Pagination :pagination="requests.meta" />
  </div>
</template>
