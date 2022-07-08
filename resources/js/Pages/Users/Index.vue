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
      <div class="flex-1 grid grid-cols-1 p-4 md:grid-cols-3 gap-4">
        <div class="relative">
          <div class="pointer-events-none absolute inset-y-0 left-0 pl-3 flex items-center">
            <SearchIcon class="w-5 h-5 text-gray-400" />
          </div>
          <input
            v-model.trim="form.search"
            type="search"
            class="block py-2 pr-3 pl-10 w-full max-w-lg sm:text-sm placeholder:text-gray-500 focus:text-gray-900 focus:placeholder:text-gray-400 bg-white rounded-md border border-gray-300 focus:border-blumilk-500 focus:outline-none focus:ring-1 focus:ring-blumilk-500 sm:text-sm"
            placeholder="Szukaj"
          >
        </div>
        <Listbox
          v-model="form.status"
          as="div"
        >
          <div class="relative">
            <ListboxButton
              class="relative py-2 pr-10 pl-3 w-full max-w-lg text-left bg-white rounded-md border border-gray-300 focus:border-blumilk-500 focus:outline-none focus:ring-1 focus:ring-blumilk-500 shadow-sm cursor-default sm:text-sm"
            >
              <span class="block truncate">
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
      </div>
      <div class="overflow-auto xl:overflow-visible relative">
        <div
          v-if="selectedUsers.length > 0"
          class="flex absolute top-0 left-20 h-10 items-center bg-gray-50"
        >
          <button
            type="button"
            class="items-center rounded border border-gray-300 bg-white px-2 py-1 text-xs font-medium text-gray-500 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blumilk-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-30"
            @click="copyEmails()"
          >
            Skopiuj adresy e-mail
          </button>
        </div>
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th
                scope="col"
                class="relative w-16 px-8"
              >
                <input
                  type="checkbox"
                  class="absolute left-6 top-1/2 -mt-2 h-4 w-4 rounded border-gray-300 text-blumilk-600 focus:ring-blumilk-500"
                  :disabled="users.data.length === 0"
                  :checked="indeterminate || selectedUsers.length === users.data.length && users.data.length > 0"
                  :indeterminate="indeterminate"
                  @change="selectedUsers = $event.target.checked ? users.data.map((user) => {
                    return {
                      email: user.email,
                      name: user.name
                    }
                  }) : []"
                >
              </th>
              <th
                scope="col"
                class="py-3 px-4 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase whitespace-nowrap"
              >
                <span v-if="selectedUsers.length === 0">
                  Imię i nazwisko
                </span>
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
              :class="[ selectedUsers.find((selectedUser) => selectedUser.email === user.email) && 'bg-blumilk-25', { 'bg-red-50': user.deleted, 'hover:bg-blumilk-25': !user.deleted }]"
            >
              <td class="relative w-12 px-6 sm:w-16 sm:px-8">
                <div
                  v-if="selectedUsers.find((selectedUser) => selectedUser.email === user.email)"
                  class="absolute inset-y-0 left-0 w-0.5 bg-blumilk-600"
                />
                <input
                  v-model="selectedUsers"
                  type="checkbox"
                  class="absolute left-6 top-1/2 -mt-2 h-4 w-4 rounded border-gray-300 text-blumilk-600 focus:ring-blumilk-500"
                  :value="{email: user.email, name:user.name}"
                >
              </td>
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
                  <MenuButton
                    class="flex items-center text-gray-400 hover:text-gray-600 rounded-full focus:outline-none focus:ring-2 focus:ring-blumilk-500 focus:ring-offset-2 focus:ring-offset-gray-100"
                  >
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
                    <MenuItems
                      class="absolute right-0 z-10 mt-2 w-56 bg-white rounded-md focus:outline-none ring-1 ring-black ring-opacity-5 shadow-lg origin-top-right"
                    >
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
                            @click="form.status.value !== 'all' ? removeUser(user): null"
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
                            @click="form.status.value !== 'all' ? removeUser(user): null"
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
import { ref, watch, computed, reactive } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import { debounce } from 'lodash'
import { SearchIcon } from '@heroicons/vue/outline'
import { DotsVerticalIcon, PencilIcon, BanIcon, RefreshIcon, SelectorIcon, CheckIcon } from '@heroicons/vue/solid'
import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue'
import { Listbox, ListboxButton, ListboxOption, ListboxOptions } from '@headlessui/vue'
import { DateTime } from 'luxon'
import { useToast } from 'vue-toastification'
import Pagination from '@/Shared/Pagination'
import EmptyState from '@/Shared/Feedbacks/EmptyState'

const props = defineProps({
  users: Object,
  filters: Object,
})

const statuses = [
  {
    name: 'Aktywni użytkownicy',
    value: 'active',
  },
  {
    name: 'Nieaktywni użytkownicy',
    value: 'inactive',
  },
  {
    name: 'Wszyscy',
    value: 'all',
  },
]

const form = reactive({
  search: props.filters.search,
  status: statuses.find(status => status.value === props.filters.status) ?? statuses[0],
})

const toast = useToast()
const selectedUsers = ref([])
const indeterminate = computed(() => selectedUsers.value.length > 0 && selectedUsers.value.length < props.users.data.length)

function copyEmails(){
  const emails = selectedUsers.value.map((user) => `"${user.name}" <${user.email}>`).join(', ')
  navigator.clipboard.writeText(emails)
  selectedUsers.value = []
  toast.info('Skopiowano adresy e-mail do schowka.')
}

function removeUser(user){
  selectedUsers.value = selectedUsers.value.filter((selectedUser) => selectedUser.email !== user.email)
}

watch(form, debounce(() => {
  selectedUsers.value = []

  Inertia.get('/users', {
    search: form.search,
    status: form.status.value,
  }, {
    preserveState: true,
    replace: true,
  })
}, 300))
</script>
