<script setup>
import { EllipsisVerticalIcon, PencilIcon, TrashIcon } from '@heroicons/vue/24/solid'
import { FlagIcon } from '@heroicons/vue/24/outline'
import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue'
import EmptyState from '@/Shared/Feedbacks/EmptyState.vue'
import YearPicker from '@/Shared/Forms/YearPicker.vue'
import { ref, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import { DateTime } from 'luxon'
import InertiaLink from '@/Shared/InertiaLink.vue'
import { useGlobalProps } from '@/Composables/useGlobalProps'
import AppLayout from '@/Shared/Layout/AppLayout.vue'

const props = defineProps({
  holidays: Object,
  year: Number,
})

const selectedYear = ref(props.year)
const currentDate = DateTime.now()
const { auth } = useGlobalProps()

watch(selectedYear, (value, oldValue) => {
  if (value === oldValue)
    return

  router.visit('/holidays', {
    data: { year: value },
  })
})
</script>

<template>
  <AppLayout title="Dni wolne od pracy">
    <div class="bg-white shadow-md">
      <div class="flex justify-between items-center p-4 sm:px-6">
        <div>
          <h2 class="text-lg font-medium leading-6 text-gray-900">
            Dni wolne od pracy w roku
            <YearPicker
              v-model="selectedYear"
              :from="currentDate.year + 1"
              :to="currentDate.year - 20"
              class="inline-block ml-2"
            />
          </h2>
        </div>
        <div v-if="auth.can.manageHolidays">
          <InertiaLink
            href="holidays/create"
            class="inline-flex items-center py-3 px-4 text-sm font-medium leading-4 text-white bg-blumilk-600 hover:bg-blumilk-700 rounded-md border border-transparent focus:outline-none focus:ring-2 focus:ring-blumilk-500 focus:ring-offset-2 shadow-sm"
          >
            Dodaj dzień
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
                  Nazwa
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
              <tr
                v-for="holiday in holidays.data"
                :key="holiday.id"
                :class="[holiday.isPast ? 'bg-gray-100' : 'hover:bg-blumilk-25']"
              >
                <td class="p-4 text-sm font-semibold text-gray-700 capitalize whitespace-nowrap">
                  {{ holiday.name }}
                </td>
                <td class="p-4 text-sm text-gray-500 whitespace-nowrap">
                  {{ holiday.displayDate }}
                </td>
                <td class="p-4 text-sm text-gray-500 whitespace-nowrap flex items-center gap-1">
                  {{ holiday.dayOfWeek }}
                  <span
                    v-if="holiday.isSaturday"
                    v-tooltip.right="'sobota do odebrania'"
                    class="inline-block"
                  >
                    <FlagIcon class="size-5 text-red-500" />
                  </span>
                </td>
                <td class="p-4 text-sm text-right text-gray-500 whitespace-nowrap">
                  <Menu
                    v-if="auth.can.manageHolidays"
                    as="div"
                    class="inline-block relative text-left"
                  >
                    <MenuButton class="flex items-center text-gray-400 hover:text-gray-600 rounded-full focus:outline-none focus:ring-2 focus:ring-blumilk-500 focus:ring-offset-2 focus:ring-offset-gray-100">
                      <EllipsisVerticalIcon
                        class="size-5"
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
                      <MenuItems class="absolute right-0 z-10 mt-2 w-56 bg-white rounded-md focus:outline-none ring-1 ring-black/5 shadow-lg origin-top-right">
                        <div class="py-1">
                          <MenuItem
                            v-slot="{ active }"
                            class="flex"
                          >
                            <InertiaLink
                              :href="`/holidays/${holiday.id}/edit`"
                              :class="[active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'font-medium block px-4 py-2 text-sm']"
                            >
                              <PencilIcon class="mr-2 size-5 text-blue-500" /> Edytuj
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
                              <TrashIcon class="mr-2 size-5 text-red-500" /> Usuń
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
                  class="py-4 text-xl leading-5 text-center text-gray-700"
                >
                  <EmptyState>
                    <template #title>
                      Brak dni wolnych od pracy
                    </template>
                    <template #text>
                      Brak wpisów dotyczących dni wolnych
                    </template>
                  </EmptyState>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
