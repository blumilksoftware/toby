<template>
  <InertiaHead title="Użytkownicy" />
  <div class="bg-white shadow-md">
    <div class="flex justify-between items-center p-4 sm:px-6">
      <div>
        <h2 class="text-lg leading-6 font-medium text-gray-900">
          Użytkownicy
        </h2>
      </div>
      <div>
        <InertiaLink
          href="users/create"
          class="inline-flex items-center text-center px-4 py-3 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-blumilk-600 hover:bg-blumilk-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blumilk-500"
        >
          Dodaj użytkownika
        </InertiaLink>
      </div>
    </div>
    <div class="border-t border-gray-200">
      <div class="px-4 py-3">
        <div class="relative max-w-md">
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <SearchIcon class="h-5 w-5 text-gray-400" />
          </div>
          <input
            v-model.trim="search"
            type="search"
            class="block w-full bg-white border border-gray-300 rounded-md py-2 pl-10 pr-3 text-sm placeholder-gray-500 focus:outline-none focus:text-gray-900 focus:placeholder-gray-400 focus:ring-1 focus:ring-blumilk-500 focus:border-blumilk-500 sm:text-sm mt-1"
            placeholder="Szukaj"
          >
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
                Imię i nazwisko
              </th>
              <th
                scope="col"
                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap"
              >
                Rola
              </th>
              <th
                scope="col"
                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap"
              >
                Stanowisko
              </th>
              <th
                scope="col"
                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap"
              >
                Forma zatrudnienia
              </th>
              <th
                scope="col"
                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap"
              >
                Data rozpoczęcia
              </th>
              <th
                scope="col"
                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap"
              />
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-100">
            <tr
              v-for="user in users.data"
              :key="user.id"
              :class="{ 'bg-red-50': user.deleted, 'hover:bg-blumilk-25': !user.deleted }"
            >
              <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                <div class="flex">
                  <span class="inline-flex items-center justify-center h-10 w-10 rounded-full">
                    <img
                      class="h-10 w-10 rounded-full"
                      :src="user.avatar"
                    >
                  </span>
                  <div class="ml-3">
                    <p class="text-sm font-medium break-all text-gray-900">
                      {{ user.name }}
                    </p>
                    <p class="text-sm break-all text-gray-500">
                      {{ user.email }}
                    </p>
                  </div>
                </div>
              </td>
              <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ user.role }}
              </td>
              <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ user.position }}
              </td>
              <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ user.employmentForm }}
              </td>
              <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ user.employmentDate }}
              </td>
              <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 text-right">
                <Menu
                  as="div"
                  class="relative inline-block text-left"
                >
                  <MenuButton class="rounded-full flex items-center text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 focus:ring-blumilk-500">
                    <DotsVerticalIcon class="h-5 w-5" />
                  </MenuButton>

                  <transition
                    enter-active-class="transition ease-out duration-100"
                    enter-from-class="transform opacity-0 scale-95"
                    enter-to-class="transform opacity-100 scale-100"
                    leave-active-class="transition ease-in duration-75"
                    leave-from-class="transform opacity-100 scale-100"
                    leave-to-class="transform opacity-0 scale-95"
                  >
                    <MenuItems class="origin-top-right absolute right-0 mt-2 w-56 z-10 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none">
                      <div
                        v-if="!user.deleted"
                        class="py-1"
                      >
                        <MenuItem
                          v-slot="{ active }"
                          class="flex"
                        >
                          <InertiaLink
                            :href="`/users/${user.id}/edit`"
                            :class="[active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'font-medium block px-4 py-2 text-sm']"
                          >
                            <PencilIcon class="mr-2 h-5 w-5 text-blue-500" /> Edytuj
                          </InertiaLink>
                        </MenuItem>
                        <MenuItem
                          v-slot="{ active }"
                          class="flex"
                        >
                          <InertiaLink
                            as="button"
                            method="delete"
                            :preserve-scroll="true"
                            :href="`/users/${user.id}`"
                            :class="[active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'block w-full text-left font-medium px-4 py-2 text-sm']"
                          >
                            <TrashIcon class="mr-2 h-5 w-5 text-red-500" /> Usuń
                          </InertiaLink>
                        </MenuItem>
                      </div>
                      <div
                        v-else
                        class="py-1"
                      >
                        <MenuItem
                          v-slot="{ active }"
                          class="flex"
                        >
                          <InertiaLink
                            as="button"
                            method="post"
                            :preserve-scroll="true"
                            :href="`/users/${user.id}/restore`"
                            :class="[active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'block w-full text-left font-medium px-4 py-2 text-sm']"
                          >
                            <RefreshIcon class="mr-2 h-5 w-5 text-green-500" /> Przywróć
                          </InertiaLink>
                        </MenuItem>
                      </div>
                    </MenuItems>
                  </transition>
                </Menu>
              </td>
            </tr>
            <tr
              v-if="! users.data.length"
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
        <Pagination :pagination="users.meta" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import { debounce } from 'lodash'
import { SearchIcon } from '@heroicons/vue/outline'
import { DotsVerticalIcon, PencilIcon, TrashIcon, RefreshIcon } from '@heroicons/vue/solid'
import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue'
import Pagination from '@/Shared/Pagination'

const props = defineProps({
  users: Object,
  filters: Object,
})

const search = ref(props.filters.search)

watch(search, debounce(value => {
  Inertia.get('/users', value ? { search: value } : {}, {
    preserveState: true,
    replace: true,
  })
}, 300))
</script>
