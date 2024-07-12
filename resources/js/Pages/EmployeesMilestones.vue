<script setup>
import EmptyState from '@/Shared/Feedbacks/EmptyState.vue'
import { MagnifyingGlassIcon } from '@heroicons/vue/24/outline'
import { reactive, watch } from 'vue'
import { debounce } from 'lodash'
import { Inertia } from '@inertiajs/inertia'
import { CakeIcon, CalendarDaysIcon, CheckIcon, ChevronUpDownIcon } from '@heroicons/vue/24/solid'
import { Listbox, ListboxButton, ListboxOption, ListboxOptions } from '@headlessui/vue'
import UserProfileLink from '@/Shared/UserProfileLink.vue'

const sortOptions = [
  {
    name: 'Sortowanie domyślne',
    value: null,
  },
  {
    name: 'Urodziny od najbliższej daty',
    value: 'birthday-asc',
  },
  {
    name: 'Urodziny od najdalszej daty',
    value: 'birthday-desc',
  },
  {
    name: 'Najdłuższy staż pracy',
    value: 'seniority-asc',
  },
  {
    name: 'Najkrótszy staż pracy',
    value: 'seniority-desc',
  },
]

const props = defineProps({
  users: Object,
  filters: Object,
})

const form = reactive({
  search: props.filters.search,
  sort: sortOptions.find(item => item.value === props.filters.sort) ?? sortOptions[0],
})

watch(form, debounce(() => {
  Inertia.get('/employees-milestones', {
    search: form.search,
    sort: form.sort?.value ?? undefined,
  }, {
    preserveState: true,
    replace: true,
  })
}, 300))

</script>

<template>
  <InertiaHead title="Jubileusze" />
  <div class="bg-white shadow-md">
    <div class="flex justify-between items-center p-4 sm:px-6">
      <div>
        <h2 class="text-lg font-medium leading-6 text-gray-900">
          Jubileusze
        </h2>
      </div>
    </div>
    <div class="border-t border-gray-200">
      <div class="flex-1 grid grid-cols-1 p-4 md:grid-cols-3 gap-4">
        <div class="relative">
          <div class="pointer-events-none absolute inset-y-0 left-0 pl-3 flex items-center">
            <MagnifyingGlassIcon class="w-5 h-5 text-gray-400" />
          </div>
          <input
            v-model.trim="form.search"
            class="block py-2 pr-3 pl-10 w-full max-w-lg placeholder:text-gray-500 focus:text-gray-900 focus:placeholder:text-gray-400 bg-white rounded-md border border-gray-300 focus:border-blumilk-500 focus:outline-none focus:ring-1 focus:ring-blumilk-500 sm:text-sm"
            placeholder="Szukaj"
            type="search"
          >
        </div>
        <Listbox
          v-model="form.sort"
          as="div"
        >
          <div class="relative mt-1 sm:mt-0">
            <ListboxButton
              class="relative py-2 pr-10 pl-3 w-full max-w-lg text-left bg-white rounded-md border border-gray-300 focus:border-blumilk-500 focus:outline-none focus:ring-1 focus:ring-blumilk-500 shadow-sm cursor-default sm:text-sm"
            >
              <span class="flex items-center">
                {{ form.sort.name }}
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
                  v-for="option in sortOptions"
                  :key="option.value"
                  v-slot="{ active, selected }"
                  :value="option"
                  as="template"
                >
                  <li
                    :class="[active ? 'bg-gray-100' : 'text-gray-900', 'cursor-default truncate select-none relative py-2 pl-3 pr-9']"
                  >
                    {{ option.name }}

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
      </div>
      <div class="overflow-auto xl:overflow-visible relative">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th
                class="py-3 px-4 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase whitespace-nowrap"
                scope="col"
              >
                <span>
                  Imię i nazwisko
                </span>
              </th>
              <th
                class="py-3 px-4 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase whitespace-nowrap"
                scope="col"
              >
                Następne urodziny
              </th>
              <th
                class="py-3 px-4 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase whitespace-nowrap"
                scope="col"
              >
                Data zatrudnienia
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-100">
            <tr
              v-for="user in users.data"
              :key="user.id"
            >
              <td class="p-4 text-sm text-gray-500 whitespace-nowrap">
                <UserProfileLink
                  :user="user.user"
                >
                  <div class="flex">
                    <span class="inline-flex justify-center items-center w-10 h-10 rounded-full">
                      <img
                        :src="user.user.avatar"
                        class="w-10 h-10 rounded-full"
                      >
                    </span>
                    <div class="ml-3">
                      <p class="text-sm font-medium text-gray-900 break-all">
                        {{ user.user.name }}
                      </p>
                      <p class="text-sm text-gray-500 break-all">
                        {{ user.user.email }}
                      </p>
                    </div>
                  </div>
                </UserProfileLink>
              </td>
              <td class="p-4 text-sm text-gray-500 whitespace-nowrap">
                <div class="flex flex-col gap-1">
                  <div>
                    {{ user.birthdayDisplayDate }}
                  </div>
                  <div class="flex items-center gap-1 font-medium">
                    {{ user.birthdayRelativeDate }}
                    <span
                      v-if="user.isBirthdayToday"
                      v-tooltip.right="'urodziny'"
                      class="inline-block"
                    >
                      <CakeIcon class="w-5 h-5 text-violet-400" />
                    </span>
                  </div>
                </div>
              </td>
              <td class="p-4 text-sm text-gray-500 whitespace-nowrap">
                <div class="flex flex-col gap-1">
                  <div>
                    {{ user.employmentDate }}
                  </div>
                  <div class="flex items-center gap-1 font-medium">
                    <div>{{ user.seniorityDisplayDate ? user.seniorityDisplayDate : '-' }}</div>
                    <span
                      v-if="user.isWorkAnniversaryToday"
                      v-tooltip.right="'okrągła rocznica pracy'"
                      class="inline-block"
                    >
                      <CalendarDaysIcon class="w-5 h-5 text-pink-400" />
                    </span>
                  </div>
                </div>
              </td>
            </tr>
            <tr
              v-if="! users.data.length"
            >
              <td
                class="py-4 text-xl leading-5 text-center text-gray-700"
                colspan="100%"
              >
                <EmptyState>
                  <template #title>
                    Nie znaleziono użytkownika
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
    </div>
  </div>
</template>
