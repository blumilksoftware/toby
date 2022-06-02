<template>
  <InertiaHead title="Użytkownicy" />
  <div class="bg-white shadow-md">
    <div class="flex justify-between items-center p-4 sm:px-6">
      <div>
        <h2 class="text-lg font-medium leading-6 text-gray-900">
          Użytkownicy
        </h2>
      </div>
      <div>
        <InertiaLink
          href="users/create"
          class="inline-flex items-center py-3 px-4 text-sm font-medium leading-4 text-center text-white bg-blumilk-600 hover:bg-blumilk-700 rounded-md border border-transparent focus:outline-none focus:ring-2 focus:ring-blumilk-500 focus:ring-offset-2 shadow-sm"
        >
          Dodaj użytkownika
        </InertiaLink>
      </div>
    </div>
    <div class="border-t border-gray-200">
      <div class="py-3 px-4">
        <div class="relative max-w-md">
          <div class="flex absolute inset-y-0 left-0 items-center pl-3 pointer-events-none">
            <SearchIcon class="w-5 h-5 text-gray-400" />
          </div>
          <input
            v-model.trim="search"
            type="search"
            class="block py-2 pr-3 pl-10 mt-1 w-full text-sm placeholder:text-gray-500 focus:text-gray-900 focus:placeholder:text-gray-400 bg-white rounded-md border border-gray-300 focus:border-blumilk-500 focus:outline-none focus:ring-1 focus:ring-blumilk-500 sm:text-sm"
            placeholder="Szukaj"
          >
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
                Imię i nazwisko
              </th>
              <th
                scope="col"
                class="py-3 px-4 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase whitespace-nowrap"
              >
                Rola
              </th>
              <th
                scope="col"
                class="py-3 px-4 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase whitespace-nowrap"
              >
                Ostatnia aktywność
              </th>
              <th
                scope="col"
                class="py-3 px-4 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase whitespace-nowrap"
              >
                Stanowisko
              </th>
              <th
                scope="col"
                class="py-3 px-4 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase whitespace-nowrap"
              >
                Forma zatrudnienia
              </th>
              <th
                scope="col"
                class="py-3 px-4 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase whitespace-nowrap"
              >
                Data rozpoczęcia
              </th>
              <th
                scope="col"
                class="py-3 px-4 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase whitespace-nowrap"
              />
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-100">
            <tr
              v-for="user in users.data"
              :key="user.id"
              :class="{ 'bg-red-50': user.deleted, 'hover:bg-blumilk-25': !user.deleted }"
            >
              <td class="p-4 text-sm text-gray-500 whitespace-nowrap">
                <div class="flex">
                  <span class="inline-flex justify-center items-center w-10 h-10 rounded-full">
                    <img
                      class="w-10 h-10 rounded-full"
                      :src="user.avatar"
                    >
                  </span>
                  <div class="ml-3">
                    <p class="text-sm font-medium text-gray-900 break-all">
                      {{ user.name }}
                    </p>
                    <p class="text-sm text-gray-500 break-all">
                      {{ user.email }}
                    </p>
                  </div>
                </div>
              </td>
              <td class="p-4 text-sm text-gray-500 whitespace-nowrap">
                {{ user.role }}
              </td>
              <td class="p-4 text-sm text-gray-500 whitespace-nowrap">
                {{ user.lastActiveAt ? DateTime.fromSQL(user.lastActiveAt).toRelative() : '-' }}
              </td>
              <td class="p-4 text-sm text-gray-500 whitespace-nowrap">
                {{ user.position }}
              </td>
              <td class="p-4 text-sm text-gray-500 whitespace-nowrap">
                {{ user.employmentForm }}
              </td>
              <td class="p-4 text-sm text-gray-500 whitespace-nowrap">
                {{ user.employmentDate }}
              </td>
              <td class="p-4 text-sm text-right text-gray-500 whitespace-nowrap">
                <Menu
                  as="div"
                  class="inline-block relative text-left"
                >
                  <MenuButton class="flex items-center text-gray-400 hover:text-gray-600 rounded-full focus:outline-none focus:ring-2 focus:ring-blumilk-500 focus:ring-offset-2 focus:ring-offset-gray-100">
                    <DotsVerticalIcon class="w-5 h-5" />
                  </MenuButton>

                  <transition
                    enter-active-class="transition ease-out duration-100"
                    enter-from-class="transform opacity-0 scale-95"
                    enter-to-class="transform opacity-100 scale-100"
                    leave-active-class="transition ease-in duration-75"
                    leave-from-class="transform opacity-100 scale-100"
                    leave-to-class="transform opacity-0 scale-95"
                  >
                    <MenuItems class="absolute right-0 z-10 mt-2 w-56 bg-white rounded-md focus:outline-none ring-1 ring-black ring-opacity-5 shadow-lg origin-top-right">
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
                            <PencilIcon class="mr-2 w-5 h-5 text-blue-500" /> Edytuj
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
                            <BanIcon class="mr-2 w-5 h-5 text-red-500" /> Zablokuj
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
                            <RefreshIcon class="mr-2 w-5 h-5 text-green-500" /> Przywróć
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
                class="py-4 text-xl leading-5 text-center text-gray-700"
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
      <Pagination :pagination="users.meta" />
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import { debounce } from 'lodash'
import { SearchIcon } from '@heroicons/vue/outline'
import { DotsVerticalIcon, PencilIcon, BanIcon, RefreshIcon } from '@heroicons/vue/solid'
import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue'
import { DateTime } from 'luxon'
import Pagination from '@/Shared/Pagination'
import EmptyState from '@/Shared/Feedbacks/EmptyState'

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
