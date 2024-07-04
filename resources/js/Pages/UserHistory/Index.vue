<script setup>
import {
  EllipsisVerticalIcon,
  PencilIcon,
  TrashIcon,
} from '@heroicons/vue/24/solid'
import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue'
import EmptyState from '@/Shared/Feedbacks/EmptyState.vue'

defineProps({
  auth: Object,
  history: Object,
  userId: Number,
})


</script>

<template>
  <InertiaHead title="Historia użytkownika" />
  <div class="bg-white shadow-md">
    <div class="flex justify-between items-center p-4 sm:px-6">
      <div>
        <h2 class="text-lg font-medium leading-6 text-gray-900">
          Historia
        </h2>
      </div>
      <div>
        <InertiaLink
          class="inline-flex items-center py-3 px-4 text-sm font-medium leading-4 text-center text-white bg-blumilk-600 hover:bg-blumilk-700 rounded-md border border-transparent focus:outline-none focus:ring-2 focus:ring-blumilk-500 focus:ring-offset-2 shadow-sm"
          :href="`/users/${userId}/history/create`"
        >
          Dodaj wpis
        </InertiaLink>
      </div>
    </div>
    <div class="border-t border-gray-200">
      <div class="overflow-auto 2xl:overflow-visible relative">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th
                class="py-3 px-4 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase whitespace-nowrap"
                scope="col"
              >
                Typ zdarzenia
              </th>
              <th
                class="py-3 px-4 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase whitespace-normal"
                scope="col"
              >
                Komentarz
              </th>
              <th
                class="py-3 px-4 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase whitespace-normal"
                scope="col"
              >
                Od
              </th>
              <th
                class="py-3 px-4 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase whitespace-normal"
                scope="col"
              >
                Do
              </th>
              <th
                class="py-3 px-4 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase whitespace-nowrap"
                scope="col"
              />
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-100">
            <tr
              v-for="item in history.data"
              :key="item.id"
              class="bg-blumilk-25"
            >
              <td class="p-4 text-sm text-gray-500 whitespace-nowrap flex items-center">
                {{ item.typeLabel }}
                <span
                  v-if="item.type === 'employment'"
                  class="flex items-center"
                >
                  ({{ item.employmentFormLabel }})
                  <img
                    v-if="item.isEmployedAtCurrentCompany === true"
                    class="h-4 text0red-500 ml-3"
                    src="/images/logo.svg"
                  >
                </span>
              </td>
              <td class="p-4 text-sm text-gray-500 whitespace-nowrap">
                {{ item.comment || '-' }}
              </td>
              <td class="p-4 text-sm text-gray-500 whitespace-normal">
                {{ item.from }}
              </td>
              <td class="p-4 text-sm text-gray-500 whitespace-nowrap">
                {{ item.to || '-' }}
              </td>
              <td class="p-4 text-sm text-right text-gray-500 whitespace-nowrap">
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
                      class="absolute right-0 z-10 my-2 w-56 bg-white rounded-md focus:outline-none ring-1 ring-black ring-opacity-5 shadow-lg origin-top-right"
                    >
                      <div class="py-1">
                        <MenuItem
                          v-slot="{ active }"
                          class="flex"
                        >
                          <InertiaLink
                            :class="[active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'font-medium block px-4 py-2 text-sm']"
                            :href="`/users/history/${item.id}`"
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
                            :class="[active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'font-medium block px-4 py-2 text-sm']"
                            :method="'delete'"
                            :href="`/users/history/${item.id}`"
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
            <tr
              v-if="!history.data.length"
            >
              <td
                class="py-4 text-xl leading-5 text-center text-gray-700"
                colspan="100%"
              >
                <EmptyState>
                  <template #title>
                    Brak wyników
                  </template>
                  <template #text>
                    Dodaj pierwszy wpis
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
