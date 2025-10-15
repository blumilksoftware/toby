<script setup>
import { CheckIcon, ChevronRightIcon, ChevronUpDownIcon } from '@heroicons/vue/24/solid'
import Status from '@/Shared/Status.vue'
import { watch, reactive } from 'vue'
import { debounce } from 'lodash'
import { router } from '@inertiajs/vue3'
import { Listbox, ListboxButton, ListboxLabel, ListboxOption, ListboxOptions } from '@headlessui/vue'
import Pagination from '@/Shared/Pagination.vue'
import EmptyState from '@/Shared/Feedbacks/EmptyState.vue'
import SettlementType from '@/Shared/SettlementType.vue'
import { useMonthInfo } from '@/Composables/monthInfo.js'
import InertiaLink from '@/Shared/InertiaLink.vue'
import { useGlobalProps } from '@/Composables/useGlobalProps'
import AppLayout from '@/Shared/Layout/AppLayout.vue'

const props = defineProps({
  requests: Object,
  users: Object,
  filters: Object,
})

const { auth } = useGlobalProps()

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
  {
    name: 'Rozliczone',
    value: 'settled',
  },
]

const months = useMonthInfo().getMonths()

const form = reactive({
  user: props.users.data.find(user => user.id === props.filters.user) ?? null,
  status: statuses.find(status => status.value === props.filters.status) ?? statuses[0],
  month: months.find(month => month.value === props.filters.month),
})

watch(form, debounce(() => {
  router.get('/overtime/requests', {
    user: form.user?.id,
    status: form.status.value,
    month: form.month.value,
  }, {
    preserveState: true,
    replace: true,
  })
}, 150))
</script>

<template>
  <AppLayout title="Wnioski">
    <div class="bg-white shadow-md">
      <div class="flex justify-between items-center p-4 sm:px-6">
        <div>
          <h2 class="text-lg font-medium leading-6 text-gray-900">
            Lista wniosków
          </h2>
        </div>
        <div
          v-if="auth.can.downloadOvertimeSummary"
          class="flex"
        >
          <a
            :href="`/overtime/timesheet/${filters.month}`"
            class="block py-3 px-4 ml-3 text-sm font-medium leading-4 text-center text-white bg-blumilk-600 hover:bg-blumilk-700 rounded-md border border-transparent focus:outline-none focus:ring-2 focus:ring-blumilk-500 focus:ring-offset-2 shadow-sm"
          >
            Pobierz nadgodziny
          </a>
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
                    class="shrink-0 size-6 rounded-full"
                  >
                  <span class="block ml-3 truncate">{{ form.user.name }}</span>
                </span>
                <span class="flex absolute inset-y-0 right-0 items-center pr-2 pointer-events-none">
                  <ChevronUpDownIcon class="size-5 text-gray-400" />
                </span>
              </ListboxButton>

              <transition
                leave-active-class="transition ease-in duration-100"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
              >
                <ListboxOptions
                  class="overflow-auto absolute z-10 py-1 mt-1 w-full max-w-lg max-h-60 text-base bg-white rounded-md focus:outline-none ring-1 ring-black/5 shadow-lg sm:text-sm"
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
                        <CheckIcon class="size-5" />
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
                          class="shrink-0 size-6 rounded-full"
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
                        <CheckIcon class="size-5" />
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
                  <ChevronUpDownIcon class="size-5 text-gray-400" />
                </span>
              </ListboxButton>

              <transition
                leave-active-class="transition ease-in duration-100"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
              >
                <ListboxOptions
                  class="overflow-auto absolute z-10 py-1 mt-1 w-full max-w-lg max-h-60 text-base bg-white rounded-md focus:outline-none ring-1 ring-black/5 shadow-lg sm:text-sm"
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
                        <CheckIcon class="size-5" />
                      </span>
                    </li>
                  </ListboxOption>
                </ListboxOptions>
              </transition>
            </div>
          </Listbox>
          <Listbox
            v-model="form.month"
            as="div"
          >
            <ListboxLabel class="block mb-2 text-sm font-medium text-gray-700">
              Miesiąc
            </ListboxLabel>
            <div class="relative mt-1 sm:mt-0">
              <ListboxButton
                class="relative py-2 pr-10 pl-3 w-full max-w-lg h-10 text-left bg-white rounded-md border border-gray-300 focus:border-blumilk-500 focus:outline-none focus:ring-1 focus:ring-blumilk-500 shadow-sm cursor-default sm:text-sm"
              >
                <span class="flex items-center">
                  {{ form.month.name }}
                </span>
                <span class="flex absolute inset-y-0 right-0 items-center pr-2 pointer-events-none">
                  <ChevronUpDownIcon class="size-5 text-gray-400" />
                </span>
              </ListboxButton>

              <transition
                leave-active-class="transition ease-in duration-100"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
              >
                <ListboxOptions
                  class="overflow-auto absolute z-10 py-1 mt-1 w-full max-w-lg max-h-60 text-base bg-white rounded-md focus:outline-none ring-1 ring-black/5 shadow-lg sm:text-sm"
                >
                  <ListboxOption
                    v-for="month in months"
                    :key="month"
                    v-slot="{ active, selected }"
                    as="template"
                    :value="month"
                  >
                    <li
                      :class="[active ? 'bg-gray-100' : 'text-gray-900', 'cursor-default truncate select-none relative py-2 pl-3 pr-9']"
                    >
                      {{ month.name }}

                      <span
                        v-if="selected"
                        :class="['text-blumilk-600 absolute inset-y-0 right-0 flex items-center pr-4']"
                      >
                        <CheckIcon class="size-5" />
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
                Liczba godzin
              </th>
              <th
                scope="col"
                class="py-3 px-4 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase whitespace-nowrap"
              >
                Sposób rozliczenia
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
              :href="`/overtime/requests/${request.id}`"
              class="relative hover:bg-blumilk-25 hover:cursor-pointer"
            >
              <td class="p-4 text-sm text-gray-500 whitespace-nowrap">
                <InertiaLink
                  :href="`/overtime/requests/${request.id}`"
                  class="font-semibold text-blumilk-600 hover:text-blumilk-500 hover:underline focus:outline-blumilk-500"
                >
                  {{ request.name }}
                </InertiaLink>
              </td>
              <td class="p-4 text-sm font-medium text-gray-500 whitespace-nowrap">
                <div class="flex">
                  <div class="size-10 rounded-full">
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
              <td class="p-4 text-sm text-gray-500 whitespace-nowrap">
                {{ request.from }}
              </td>
              <td class="p-4 text-sm text-gray-500 whitespace-nowrap">
                {{ request.to }}
              </td>
              <td class="p-4 text-sm text-gray-500 whitespace-nowrap">
                {{ request.hours }}
              </td>
              <td class="p-4 text-sm text-gray-500 whitespace-nowrap">
                <SettlementType :type="request.settlementType" />
              </td>
              <td class="p-4 text-sm text-gray-500 whitespace-nowrap">
                <Status :status="request.state" />
              </td>
              <td class="p-4 text-sm text-gray-500 whitespace-nowrap">
                <InertiaLink
                  :href="`/overtime/requests/${request.id}`"
                  class="flex justify-around focus:outline-blumilk-500"
                >
                  <ChevronRightIcon class="block size-6 fill-blumilk-500" />
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
  </AppLayout>
</template>
