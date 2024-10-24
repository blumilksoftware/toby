<script setup>
import { CheckIcon, ChevronRightIcon, ChevronUpDownIcon } from '@heroicons/vue/24/solid'
import Status from '@/Shared/Status.vue'
import { reactive, watch } from 'vue'
import { debounce } from 'lodash'
import { router } from '@inertiajs/vue3'
import { Listbox, ListboxButton, ListboxLabel, ListboxOption, ListboxOptions } from '@headlessui/vue'
import Pagination from '@/Shared/Pagination.vue'
import EmptyState from '@/Shared/Feedbacks/EmptyState.vue'
import SettlementType from '@/Shared/SettlementType.vue'
import { DateTime } from 'luxon'
import YearPicker from '@/Shared/Forms/YearPicker.vue'
import InertiaLink from '@/Shared/InertiaLink.vue'
import { useGlobalProps } from '@/Composables/useGlobalProps'
import AppLayout from '@/Shared/Layout/AppLayout.vue'

const props = defineProps({
  requests: Object,
  users: Object,
  filters: Object,
})

const { auth } = useGlobalProps()

const currentDate = DateTime.now()

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

const form = reactive({
  user: props.users.data.find(user => user.id === props.filters.user) ?? null,
  status: statuses.find(status => status.value === props.filters.status) ?? statuses[0],
  year: props.filters.year ?? null,
})

watch(form, debounce(() => {
  router.get('/overtime/requests', {
    user: form.user?.id,
    status: form.status.value,
    year: form.year,
  }, {
    preserveState: true,
    replace: true,
  })
}, 150))
</script>

<template>
  <AppLayout>
    <template #title>Nadgodziny</template>
    <div class="bg-white shadow-md">
      <div class="flex justify-between items-center p-4 sm:px-6">
        <h2 class="text-lg font-medium leading-6 text-gray-900">
          Lista wniosków
        </h2>
        <div v-if="auth.overtimeEnabled">
          <InertiaLink
            class="inline-flex items-center py-3 px-4 text-sm font-medium leading-4 text-white bg-blumilk-600 hover:bg-blumilk-700 rounded-md border border-transparent focus:outline-none focus:ring-2 focus:ring-blumilk-500 focus:ring-offset-2 shadow-sm"
            href="/overtime/requests/create"
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
                    :value="null"
                    as="template"
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
                    :value="user"
                    as="template"
                  >
                    <li
                      :class="[active ? 'bg-gray-100' : 'text-gray-900', 'cursor-default select-none relative py-2 pl-3 pr-9', user.isActive ? '' : 'bg-gray-100']"
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
                    :value="status"
                    as="template"
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
          <YearPicker
            v-model="form.year"
            nullable
            label="Rok"
            :from="currentDate.year + 1"
            :to="currentDate.year - 20"
            class="inline-block ml-2"
          />
        </div>
      </div>
      <div class="overflow-auto xl:overflow-visible">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th
                class="py-3 px-4 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase whitespace-nowrap"
                scope="col"
              >
                Numer
              </th>
              <th
                class="py-3 px-4 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase whitespace-nowrap"
                scope="col"
              >
                Pracownik
              </th>
              <th
                class="py-3 px-4 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase whitespace-nowrap"
                scope="col"
              >
                Od
              </th>
              <th
                class="py-3 px-4 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase whitespace-nowrap"
                scope="col"
              >
                Do
              </th>
              <th
                class="py-3 px-4 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase whitespace-nowrap"
                scope="col"
              >
                Liczba godzin
              </th>
              <th
                class="py-3 px-4 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase whitespace-nowrap"
                scope="col"
              >
                Sposób rozliczenia
              </th>
              <th
                class="py-3 px-4 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase whitespace-nowrap"
                scope="col"
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
              :href="`/overtime/requests/${request.id}`"
              as="tr"
              :class="[request.user.isActive ? '' : 'bg-gray-100', 'relative hover:bg-blumilk-25 hover:cursor-pointer']"
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
                class="py-4 text-xl leading-5 text-center text-gray-700"
                colspan="100%"
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
