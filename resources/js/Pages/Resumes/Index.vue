<template>
  <InertiaHead title="CV" />
  <div class="bg-white shadow-md">
    <div class="flex justify-between items-center p-4 sm:px-6">
      <div>
        <h2 class="text-lg font-medium leading-6 text-gray-900">
          CV
        </h2>
      </div>
      <div v-if="true">
        <InertiaLink
          href="resumes/create"
          class="inline-flex items-center py-3 px-4 text-sm font-medium leading-4 text-white bg-blumilk-600 hover:bg-blumilk-700 rounded-md border border-transparent focus:outline-none focus:ring-2 focus:ring-blumilk-500 focus:ring-offset-2 shadow-sm"
        >
          Dodaj CV
        </InertiaLink>
      </div>
    </div>
    <div class="border-t border-gray-200">
      <div class="overflow-auto xl:overflow-visible">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
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
                Data
              </th>
              <th
                scope="col"
                class="py-3 px-4 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase whitespace-nowrap"
              >
                Dzień tygodnia
              </th>
              <th
                scope="col"
                class="py-3 px-4 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase whitespace-nowrap"
              />
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-100">
            <tr>
              <!--              v-for="holiday in holidays.data"-->
              <!--              :key="holiday.id"-->
              <!--              :class="[holiday.isPast ? 'bg-gray-100' : 'hover:bg-blumilk-25']"-->
              <td class="p-4 text-sm font-semibold text-gray-700 capitalize whitespace-nowrap">
                Jan Kowalski
              </td>
              <td class="p-4 text-sm text-gray-500 whitespace-nowrap">
                xd
              </td>
              <td class="p-4 text-sm text-gray-500 whitespace-nowrap">
                xd
              </td>
              <td class="p-4 text-sm text-right text-gray-500 whitespace-nowrap">
              <!--                <Menu-->
              <!--                  v-if="true"-->
              <!--                  as="div"-->
              <!--                  class="inline-block relative text-left"-->
              <!--                >-->
              <!--                  <MenuButton class="flex items-center text-gray-400 hover:text-gray-600 rounded-full focus:outline-none focus:ring-2 focus:ring-blumilk-500 focus:ring-offset-2 focus:ring-offset-gray-100">-->
              <!--                    <DotsVerticalIcon-->
              <!--                      class="w-5 h-5"-->
              <!--                      aria-hidden="true"-->
              <!--                    />-->
              <!--                  </MenuButton>-->

              <!--                  <transition-->
              <!--                    enter-active-class="transition ease-out duration-100"-->
              <!--                    enter-from-class="transform opacity-0 scale-95"-->
              <!--                    enter-to-class="transform opacity-100 scale-100"-->
              <!--                    leave-active-class="transition ease-in duration-75"-->
              <!--                    leave-from-class="transform opacity-100 scale-100"-->
              <!--                    leave-to-class="transform opacity-0 scale-95"-->
              <!--                  >-->
              <!--                    <MenuItems class="absolute right-0 z-10 mt-2 w-56 bg-white rounded-md focus:outline-none ring-1 ring-black ring-opacity-5 shadow-lg origin-top-right">-->
              <!--                      <div class="py-1">-->
              <!--                        <MenuItem-->
              <!--                          v-slot="{ active }"-->
              <!--                          class="flex"-->
              <!--                        >-->
              <!--                          <InertiaLink-->
              <!--                            :href="`/resumes/${holiday.id}/edit`"-->
              <!--                            :class="[active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'font-medium block px-4 py-2 text-sm']"-->
              <!--                          >-->
              <!--                            <PencilIcon class="mr-2 w-5 h-5 text-blue-500" /> Edytuj-->
              <!--                          </InertiaLink>-->
              <!--                        </MenuItem>-->
              <!--                        <MenuItem-->
              <!--                          v-slot="{ active }"-->
              <!--                          class="flex"-->
              <!--                        >-->
              <!--                          <InertiaLink-->
              <!--                            as="button"-->
              <!--                            method="delete"-->
              <!--                            :preserve-scroll="true"-->
              <!--                            :href="`/holidays/${holiday.id}`"-->
              <!--                            :class="[active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'block w-full text-left font-medium px-4 py-2 text-sm']"-->
              <!--                          >-->
              <!--                            <TrashIcon class="mr-2 w-5 h-5 text-red-500" /> Usuń-->
              <!--                          </InertiaLink>-->
              <!--                        </MenuItem>-->
              <!--                      </div>-->
              <!--                    </MenuItems>-->
              <!--                  </transition>-->
              <!--                </Menu>-->
              </td>
            </tr>
            <tr v-if="!true">
              <td
                colspan="100%"
                class="py-4 text-xl leading-5 text-center text-gray-700"
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
