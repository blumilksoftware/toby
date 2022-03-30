<template>
  <InertiaHead title="Dni wolne od pracy" />
  <div class="bg-white shadow-md">
    <div class="flex justify-between items-center p-4 sm:px-6">
      <div>
        <h2 class="text-lg leading-6 font-medium text-gray-900">
          Dni wolne od pracy
        </h2>
      </div>
      <div v-if="can.manageHolidays">
        <InertiaLink
          href="holidays/create"
          class="inline-flex items-center px-4 py-3 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-blumilk-600 hover:bg-blumilk-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blumilk-500"
        >
          Dodaj dzień
        </InertiaLink>
      </div>
    </div>
    <div class="border-t border-gray-200">
      <div class="overflow-x-auto xl:overflow-x-visible overflow-y-auto xl:overflow-y-visible">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th
                scope="col"
                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap"
              >
                Nazwa
              </th>
              <th
                scope="col"
                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap"
              >
                Data
              </th>
              <th
                scope="col"
                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap"
              >
                Dzień tygodnia
              </th>
              <th
                scope="col"
                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap"
              />
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-100">
            <tr
              v-for="holiday in holidays.data"
              :key="holiday.id"
              class="hover:bg-blumilk-25"
            >
              <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 font-semibold capitalize">
                {{ holiday.name }}
              </td>
              <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ holiday.displayDate }}
              </td>
              <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ holiday.dayOfWeek }}
              </td>
              <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 text-right">
                <Menu
                  v-if="can.manageHolidays"
                  as="div"
                  class="relative inline-block text-left"
                >
                  <MenuButton class="rounded-full flex items-center text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 focus:ring-blumilk-500">
                    <DotsVerticalIcon
                      class="h-5 w-5"
                      aria-hidden="true"
                    />
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
                      <div class="py-1">
                        <MenuItem
                          v-slot="{ active }"
                          class="flex"
                        >
                          <InertiaLink
                            :href="`/holidays/${holiday.id}/edit`"
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
                            :href="`/holidays/${holiday.id}`"
                            :class="[active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'block w-full text-left font-medium px-4 py-2 text-sm']"
                          >
                            <TrashIcon class="mr-2 h-5 w-5 text-red-500" /> Usuń
                          </InertiaLink>
                        </MenuItem>
                      </div>
                    </MenuItems>
                  </transition>
                </Menu>
              </td>
            </tr>
            <tr v-if="!holidays.data.length">
              <td
                colspan="100%"
                class="text-center py-4 text-xl leading-5 text-gray-700"
              >
                Brak danych
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { DotsVerticalIcon, PencilIcon, TrashIcon } from '@heroicons/vue/solid'
import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue'

defineProps({
  holidays: Object,
  can: Object,
})
</script>
