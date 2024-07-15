<script setup>
import EmptyState from '@/Shared/Feedbacks/EmptyState.vue'
import {
  Listbox,
  ListboxButton,
  ListboxOption,
  ListboxOptions,
  Menu,
  MenuButton,
  MenuItem,
  MenuItems,
} from '@headlessui/vue'
import Pagination from '@/Shared/Pagination.vue'
import { MagnifyingGlassIcon } from '@heroicons/vue/24/outline'
import { computed, reactive, watch } from 'vue'
import { debounce } from 'lodash'
import { Inertia } from '@inertiajs/inertia'
import MultipleCombobox from '@/Shared/Forms/MultipleCombobox.vue'
import {
  CheckIcon,
  ChevronUpDownIcon,
  ComputerDesktopIcon,
  EllipsisVerticalIcon,
  PencilIcon,
  TrashIcon,
  XMarkIcon,
} from '@heroicons/vue/24/solid'
import UserProfileLink from '@/Shared/UserProfileLink.vue'

const props = defineProps({
  auth: Object,
  equipmentItems: Object,
  labels: Array,
  filters: Object,
  users: Object,
})

const form = reactive({
  search: props.filters.search,
  labels: props.filters.labels ?? [],
  assignee: props.filters.assignee,
})

const selectedAssignee = computed(() => props.users.data.find(user => user.id === parseInt(form.assignee)) ?? null)

watch(form, debounce(() => {
  Inertia.get('/equipment-items', {
    search: form.search,
    labels: form.labels,
    assignee: form.assignee,
  }, {
    preserveState: true,
    replace: true,
  })
}, 300))
</script>

<template>
  <InertiaHead title="Sprzęt" />
  <div class="bg-white shadow-md">
    <div class="flex justify-between items-center p-4 sm:px-6">
      <div>
        <h2 class="text-lg font-medium leading-6 text-gray-900">
          Sprzęt
        </h2>
      </div>
      <div>
        <a
          class="inline-flex mr-2 items-center py-3 px-4 text-sm font-medium leading-4 text-center text-white bg-blumilk-600 hover:bg-blumilk-700 rounded-md border border-transparent focus:outline-none focus:ring-2 focus:ring-blumilk-500 focus:ring-offset-2 shadow-sm"
          href="equipment-items/download"
        >
          Pobierz plik Excel
        </a>
        <InertiaLink
          class="inline-flex mr-2 items-center py-3 px-4 text-sm font-medium leading-4 text-center text-white bg-blumilk-600 hover:bg-blumilk-700 rounded-md border border-transparent focus:outline-none focus:ring-2 focus:ring-blumilk-500 focus:ring-offset-2 shadow-sm"
          href="equipment-labels"
        >
          Zarządzaj etykietami
        </InertiaLink>
        <InertiaLink
          class="inline-flex items-center py-3 px-4 text-sm font-medium leading-4 text-center text-white bg-blumilk-600 hover:bg-blumilk-700 rounded-md border border-transparent focus:outline-none focus:ring-2 focus:ring-blumilk-500 focus:ring-offset-2 shadow-sm"
          href="equipment-items/create"
        >
          Dodaj sprzęt
        </InertiaLink>
      </div>
    </div>
    <div class="border-t border-gray-200">
      <div class="items-end grid grid-cols-1 p-4 md:grid-cols-3 gap-4">
        <div class="relative">
          <div class="pointer-events-none absolute inset-y-0 left-0 pl-3 flex items-center">
            <MagnifyingGlassIcon class="w-5 h-5 text-gray-400" />
          </div>
          <input
            v-model.trim="form.search"
            class="inline-block align-baseline py-2 pr-3 pl-10 w-full placeholder:text-gray-500 focus:text-gray-900 focus:placeholder:text-gray-400 bg-white rounded-md border border-gray-300 focus:border-blumilk-500 focus:outline-none focus:ring-1 focus:ring-blumilk-500 sm:text-sm"
            placeholder="Szukaj"
            type="search"
          >
        </div>
        <MultipleCombobox
          v-model="form.labels"
          :items="labels"
          placeholder="Etykiety"
        />
        <Listbox
          v-model="form.assignee"
          as="div"
        >
          <div class="relative mt-1 sm:mt-0">
            <ListboxButton
              class="relative py-2 pr-10 pl-3 w-full  h-10 text-left bg-white rounded-md border border-gray-300 focus:border-blumilk-500 focus:outline-none focus:ring-1 focus:ring-blumilk-500 shadow-sm cursor-default sm:text-sm placeholder:text-gray-500 focus:text-gray-800 focus:placeholder:text-gray-400 "
            >
              <span
                v-if="form.assignee === null"
                class="text-gray-500"
              >
                Przydzielona osoba
              </span>
              <span
                v-else-if="form.assignee === 'unassigned'"
              >
                Nieprzydzielone
              </span>
              <span
                v-else
                class="flex items-center"
              >
                <img
                  :src="selectedAssignee.avatar"
                  class="shrink-0 w-6 h-6 rounded-full"
                >
                <span class="block ml-3 truncate">{{ selectedAssignee.name }}</span>
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
                  :value="null"
                  as="template"
                >
                  <li
                    :class="[active ? 'bg-gray-100' : 'text-gray-900', 'cursor-default select-none relative py-2 pl-3 pr-9']"
                  >
                    <div class="flex items-center">
                      Wszystkie
                    </div>

                    <span
                      v-if="form.assignee === null"
                      :class="['text-blumilk-600 absolute inset-y-0 right-0 flex items-center pr-4']"
                    >
                      <CheckIcon class="w-5 h-5" />
                    </span>
                  </li>
                </ListboxOption>
                <ListboxOption
                  v-slot="{ active }"
                  :value="'unassigned'"
                  as="template"
                >
                  <li
                    :class="[active ? 'bg-gray-100' : 'text-gray-900', 'cursor-default select-none relative py-2 pl-3 pr-9']"
                  >
                    <div class="flex items-center">
                      Nieprzydzielone
                    </div>

                    <span
                      v-if="form.assignee === 'unassigned'"
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
                  :value="user.id"
                  as="template"
                >
                  <li
                    :class="[active ? 'bg-gray-100' : 'text-gray-900', 'cursor-default select-none relative py-2 pl-3 pr-9', user.isActive ? '' : 'bg-gray-100']"
                  >
                    <div class="flex items-center">
                      <img
                        :src="user.avatar"
                        class="shrink-0 w-6 h-6 rounded-full"
                      >
                      <span
                        :class="[form.assignee?.id === user.id ? 'font-semibold' : 'font-normal', 'ml-3 block truncate']"
                      >
                        {{ user.name }}
                      </span>
                    </div>
                    <span
                      v-if="form.assignee?.id === user.id"
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
      <div class="overflow-auto xl:overflow-visible">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th
                class="py-3 px-4 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase whitespace-nowrap"
                scope="col"
              >
                ID
              </th>
              <th
                class="py-3 px-4 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase whitespace-nowrap"
                scope="col"
              >
                Nazwa
              </th>
              <th
                class="py-3 px-4 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase whitespace-nowrap"
                scope="col"
              >
                Etykiety
              </th>
              <th
                class="py-3 px-4 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase whitespace-nowrap"
                scope="col"
              >
                Czy mobilny?
              </th>
              <th
                class="py-3 px-4 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase whitespace-nowrap"
                scope="col"
              >
                Przydzielony dla
              </th>
              <th
                class="py-3 px-4 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase whitespace-nowrap"
                scope="col"
              >
                Od kiedy
              </th>
              <th
                class="py-3 px-4 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase whitespace-nowrap"
                scope="col"
              />
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-100">
            <tr
              v-for="item in equipmentItems.data"
              :key="item.id"
              class="hover:bg-blumilk-25"
              :class="[item.assignee ? item.assignee.isActive ? '' : 'bg-gray-100' : '', 'hover:bg-blumilk-25']"
            >
              <td class="p-4 text-sm text-gray-500 font-semibold whitespace-nowrap">
                {{ item.idNumber }}
              </td>
              <td class="p-4 text-sm text-gray-500 whitespace-nowrap">
                {{ item.name }}
              </td>
              <td class="p-4 text-sm text-gray-500 max-w-sm whitespace-nowrap overflow-hidden truncate">
                <template v-if="item.labels && item.labels.length !== 0">
                  <span
                    v-for="(label, index) in item.labels"
                    :key="index"
                    class="inline-flex items-center py-1.5 px-3 mr-1 my-1 rounded-lg text-sm font-medium bg-blumilk-500 text-white"
                  >
                    {{ label }}
                  </span>
                </template>
                <span v-else>-</span>
              </td>
              <td class="p-4 text-sm text-gray-500 whitespace-nowrap">
                <div v-if="item.isMobile">
                  <CheckIcon class="w-6 h-6 text-green-500" />
                </div>
                <div v-else>
                  <XMarkIcon class="w-6 h-6 text-red-500" />
                </div>
              </td>
              <td class="p-4 text-sm text-gray-500 whitespace-nowrap">
                <div
                  v-if="item.assignee"
                  class="flex"
                >
                  <UserProfileLink
                    :user="item.assignee"
                    class="flex"
                  >
                    <span class="inline-flex justify-center items-center w-10 h-10 rounded-full">
                      <img
                        :src="item.assignee.avatar"
                        class="w-10 h-10 rounded-full"
                      >
                    </span>
                    <div class="ml-3">
                      <p class="text-sm font-medium text-gray-900 break-all">
                        {{ item.assignee.name }}
                      </p>
                      <p class="text-sm text-gray-500 break-all">
                        {{ item.assignee.email }}
                      </p>
                    </div>
                  </UserProfileLink>
                </div>
                <div
                  v-else
                  class="flex"
                >
                  <span class="">-</span>
                </div>
              </td>
              <td class="p-4 text-sm text-gray-500 whitespace-nowrap">
                {{ item.displayAssignedAt || '-' }}
              </td>
              <td
                class="p-4 text-sm text-right text-gray-500 whitespace-nowrap"
              >
                <Menu
                  as="div"
                  class="inline-block relative text-left"
                >
                  <MenuButton
                    class="flex items-center text-gray-400 hover:text-gray-600 rounded-full focus:outline-none focus:ring-2 focus:ring-blumilk-500 focus:ring-offset-2 focus:ring-offset-gray-100"
                  >
                    <EllipsisVerticalIcon class="w-5 h-5" />
                  </MenuButton>

                  <transition
                    enter-active-class="transition ease-out duration-100"
                    enter-from-class="transform opacity-0 scale-95"
                    enter-to-class="transform opacity-100 scale-100"
                    leave-active-class="transition ease-in duration-75"
                    leave-from-class="transform opacity-100 scale-100"
                    leave-to-class="transform opacity-0 scale-95"
                  >
                    <MenuItems
                      class="absolute right-0 z-10 mt-2 w-56 bg-white rounded-md focus:outline-none ring-1 ring-black ring-opacity-5 shadow-lg origin-top-right"
                    >
                      <div class="py-1">
                        <MenuItem
                          v-slot="{ active }"
                          class="flex"
                        >
                          <InertiaLink
                            :class="[active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'font-medium block px-4 py-2 text-sm']"
                            :href="`/equipment-items/${item.id}/edit`"
                          >
                            <PencilIcon class="mr-2 w-5 h-5 text-blue-500" />
                            Edytuj
                          </InertiaLink>
                        </MenuItem>
                        <MenuItem
                          v-slot="{ active }"
                          class="flex"
                        >
                          <InertiaLink
                            :class="[active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'block w-full text-left font-medium px-4 py-2 text-sm']"
                            :href="`/equipment-items/${item.id}`"
                            as="button"
                            method="delete"
                            preserve-scroll
                          >
                            <TrashIcon class="mr-2 w-5 h-5 text-red-500" />
                            Usuń
                          </InertiaLink>
                        </MenuItem>
                      </div>
                    </MenuItems>
                  </transition>
                </Menu>
              </td>
            </tr>
            <tr v-if="!equipmentItems.data.length">
              <td
                class="py-4 text-xl leading-5 text-center text-gray-700"
                colspan="100%"
              >
                <EmptyState class="text-gray-700">
                  <template #head>
                    <ComputerDesktopIcon class="mx-auto w-12 h-12" />
                  </template>
                  <template #title>
                    Brak wpisów
                  </template>
                  <template #text>
                    Nie dodano ani jednego wpisu
                  </template>
                </EmptyState>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <Pagination :pagination="equipmentItems.meta" />
    </div>
  </div>
</template>
