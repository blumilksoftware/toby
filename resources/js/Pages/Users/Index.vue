<script setup>
import { computed, reactive, ref, watch } from 'vue'
import { router, Head } from '@inertiajs/vue3'
import { debounce } from 'lodash'
import { MagnifyingGlassIcon } from '@heroicons/vue/24/outline'
import {
  ArrowPathIcon,
  CheckIcon,
  ChevronUpDownIcon,
  EllipsisVerticalIcon,
  LockClosedIcon,
  NoSymbolIcon,
  PencilIcon,
  InformationCircleIcon,
} from '@heroicons/vue/24/solid'
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
import { DateTime } from 'luxon'
import { useToast } from 'vue-toastification'
import Pagination from '@/Shared/Pagination.vue'
import EmptyState from '@/Shared/Feedbacks/EmptyState.vue'
import UserProfileLink from '@/Shared/UserProfileLink.vue'
import InertiaLink from '@/Shared/InertiaLink.vue'
import { useGlobalProps } from '@/Composables/useGlobalProps'
import { useClipboard } from '@vueuse/core'
import AppLayout from '@/Shared/Layout/AppLayout.vue'

const props = defineProps({
  users: Object,
  filters: Object,
})

const { copy } = useClipboard()
const { auth } = useGlobalProps()

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

function copyEmails() {
  const emails = selectedUsers.value.map((user) => `"${user.name}" <${user.email}>`).join(', ')
  copy(emails)
  selectedUsers.value = []
  toast.info('Skopiowano adresy e-mail do schowka.')
}

function removeUser(user) {
  selectedUsers.value = selectedUsers.value.filter((selectedUser) => selectedUser.email !== user.email)
}

watch(form, debounce(() => {
  selectedUsers.value = []

  router.get('/users', {
    search: form.search,
    status: form.status.value,
  }, {
    preserveState: true,
    replace: true,
  })
}, 300))
</script>

<template>
  <AppLayout>
    <template #title>Użytkownicy</template>
    <div class="bg-white shadow-md">
      <div class="flex justify-between items-center p-4 sm:px-6">
        <div>
          <h2 class="text-lg font-medium leading-6 text-gray-900">
            Użytkownicy
          </h2>
        </div>
        <div>
          <InertiaLink
            class="inline-flex items-center py-3 px-4 text-sm font-medium leading-4 text-center text-white bg-blumilk-600 hover:bg-blumilk-700 rounded-md border border-transparent focus:outline-none focus:ring-2 focus:ring-blumilk-500 focus:ring-offset-2 shadow-sm"
            href="users/create"
          >
            Dodaj użytkownika
          </InertiaLink>
        </div>
      </div>
      <div class="border-t border-gray-200">
        <div class="flex-1 grid grid-cols-1 p-4 md:grid-cols-3 gap-4">
          <div class="relative">
            <div class="pointer-events-none absolute inset-y-0 left-0 pl-3 flex items-center">
              <MagnifyingGlassIcon class="size-5 text-gray-400" />
            </div>
            <input
              v-model.trim="form.search"
              class="block py-2 pr-3 pl-10 w-full max-w-lg placeholder:text-gray-500 focus:text-gray-900 focus:placeholder:text-gray-400 bg-white rounded-md border border-gray-300 focus:border-blumilk-500 focus:outline-none focus:ring-1 focus:ring-blumilk-500 sm:text-sm"
              placeholder="Szukaj"
              type="search"
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
        </div>
        <div class="overflow-auto 2xl:overflow-visible relative">
          <div
            v-if="selectedUsers.length > 0"
            class="flex absolute top-0 left-20 h-10 items-center bg-gray-50"
          >
            <button
              class="items-center rounded border border-gray-300 bg-white px-2 py-1 text-xs font-medium text-gray-500 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blumilk-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-30"
              type="button"
              @click="copyEmails()"
            >
              Skopiuj adresy e-mail
            </button>
          </div>
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th
                  class="relative w-16 px-8"
                  scope="col"
                >
                  <input
                    :checked="indeterminate || selectedUsers.length === users.data.length && users.data.length > 0"
                    :disabled="users.data.length === 0"
                    :indeterminate="indeterminate"
                    class="absolute left-6 top-1/2 -mt-2 size-4 rounded border-gray-300 text-blumilk-600 focus:ring-blumilk-500"
                    type="checkbox"
                    @change="selectedUsers = $event.target.checked ? users.data.map((user) => {
                      return {
                        email: user.email,
                        name: user.name
                      }
                    }) : []"
                  >
                </th>
                <th
                  class="py-3 px-4 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase whitespace-nowrap"
                  scope="col"
                >
                  <span v-if="selectedUsers.length === 0">
                    Imię i nazwisko
                  </span>
                </th>
                <th
                  class="py-3 px-4 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase whitespace-nowrap"
                  scope="col"
                >
                  Ostatnia aktywność
                </th>
                <th
                  class="py-3 px-4 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase whitespace-nowrap"
                  scope="col"
                >
                  Stanowisko
                </th>
                <th
                  class="py-3 px-4 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase whitespace-normal"
                  scope="col"
                >
                  Następne badanie lekarskie
                </th>
                <th
                  class="py-3 px-4 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase whitespace-normal"
                  scope="col"
                >
                  Następne szkolenie BHP
                </th>
                <th
                  class="py-3 px-4 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase whitespace-nowrap"
                  scope="col"
                />
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
              <tr
                v-for="user in users.data"
                :key="user.id"
                :class="[ selectedUsers.find((selectedUser) => selectedUser.email === user.email) && 'bg-blumilk-25', { 'bg-gray-100': user.deleted, 'hover:bg-blumilk-25': !user.deleted }]"
              >
                <td class="relative w-12 px-6 sm:w-16 sm:px-8">
                  <div
                    v-if="selectedUsers.find((selectedUser) => selectedUser.email === user.email)"
                    class="absolute inset-y-0 left-0 w-0.5 bg-blumilk-600"
                  />
                  <input
                    v-model="selectedUsers"
                    :value="{email: user.email, name:user.name}"
                    class="absolute left-6 top-1/2 -mt-2 size-4 rounded border-gray-300 text-blumilk-600 focus:ring-blumilk-500"
                    type="checkbox"
                  >
                </td>
                <td class="p-4 text-sm text-gray-500 whitespace-nowrap">
                  <UserProfileLink
                    :user="user"
                  >
                    <div class="flex">
                      <span class="inline-flex justify-center items-center size-10 rounded-full">
                        <img
                          :src="user.avatar"
                          class="size-10 rounded-full"
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
                  </UserProfileLink>
                </td>
                <td class="p-4 text-sm text-gray-500 whitespace-nowrap">
                  {{ user.lastActiveAt ? DateTime.fromSQL(user.lastActiveAt).toRelative() : '-' }}
                </td>
                <td class="p-4 text-sm text-gray-500 whitespace-normal">
                  {{ user.position }}
                </td>
                <td class="p-4 text-sm text-gray-500 whitespace-nowrap">
                  {{ user.nextMedicalExamDate || '-' }}
                </td>
                <td class="p-4 text-sm text-gray-500 whitespace-nowrap">
                  {{ user.nextOhsTrainingDate || '-' }}
                </td>
                <td class="p-4 text-sm text-right text-gray-500 whitespace-nowrap">
                  <Menu
                    as="div"
                    class="inline-block relative text-left"
                  >
                    <MenuButton
                      class="flex items-center text-gray-400 hover:text-gray-600 rounded-full focus:outline-none focus:ring-2 focus:ring-blumilk-500 focus:ring-offset-2 focus:ring-offset-gray-100"
                    >
                      <EllipsisVerticalIcon class="size-5" />
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
                        class="absolute right-0 z-10 mt-2 w-56 bg-white rounded-md focus:outline-none ring-1 ring-black/5 shadow-lg origin-top-right"
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
                              :class="[active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'font-medium block px-4 py-2 text-sm']"
                              :href="`/users/${user.id}/edit`"
                            >
                              <PencilIcon class="mr-2 size-5 text-blue-500" />
                              Edytuj
                            </InertiaLink>
                          </MenuItem>
                          <MenuItem
                            v-if="auth.can.managePermissions"
                            v-slot="{ active }"
                            class="flex"
                          >
                            <InertiaLink
                              :class="[active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'font-medium block px-4 py-2 text-sm']"
                              :href="`/users/${user.id}/permissions`"
                            >
                              <LockClosedIcon class="mr-2 size-5 text-yellow-500" />
                              Uprawnienia
                            </InertiaLink>
                          </MenuItem>
                          <MenuItem
                            v-slot="{ active }"
                            class="flex"
                          >
                            <InertiaLink
                              :class="[active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'font-medium block px-4 py-2 text-sm']"
                              :href="`/users/${user.id}/history`"
                            >
                              <InformationCircleIcon class="mr-2 size-5 text-violet-500" />
                              Szczegóły
                            </InertiaLink>
                          </MenuItem>
                          <MenuItem
                            v-slot="{ active }"
                            class="flex"
                          >
                            <InertiaLink
                              :class="[active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'block w-full text-left font-medium px-4 py-2 text-sm']"
                              :href="`/users/${user.id}`"
                              :preserve-scroll="true"
                              as="button"
                              method="delete"
                              @click="form.status.value !== 'all' ? removeUser(user): null"
                            >
                              <NoSymbolIcon class="mr-2 size-5 text-red-500" />
                              Zablokuj
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
                              :class="[active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'block w-full text-left font-medium px-4 py-2 text-sm']"
                              :href="`/users/${user.id}/restore`"
                              :preserve-scroll="true"
                              as="button"
                              method="post"
                              @click="form.status.value !== 'all' ? removeUser(user): null"
                            >
                              <ArrowPathIcon class="mr-2 size-5 text-green-500" />
                              Przywróć
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
        <Pagination :pagination="users.meta" />
      </div>
    </div>
  </AppLayout>
</template>
