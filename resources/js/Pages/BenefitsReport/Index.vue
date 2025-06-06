<script setup>
import { EllipsisVerticalIcon, TrashIcon } from '@heroicons/vue/24/solid'
import Pagination from '@/Shared/Pagination.vue'
import EmptyState from '@/Shared/Feedbacks/EmptyState.vue'
import { reactive, watch } from 'vue'
import { MagnifyingGlassIcon } from '@heroicons/vue/24/outline'
import { debounce } from 'lodash'
import { router } from '@inertiajs/vue3'
import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue'
import InertiaLink from '@/Shared/InertiaLink.vue'
import AppLayout from '@/Shared/Layout/AppLayout.vue'

const props = defineProps({
  benefitsReports: Object,
  filters: Object,
})

const form = reactive({
  search: props.filters.search,
})

watch(form, debounce(() => {

  router.get('/benefits-reports', {
    search: form.search,
  }, {
    preserveState: true,
    replace: true,
  })
}, 300))

</script>

<template>
  <AppLayout title="Raporty benefitowe">
    <div class="bg-white shadow-md">
      <div class="flex justify-between items-center p-4 sm:px-6">
        <h2 class="text-lg font-medium leading-6 text-gray-900">
          Raporty benefitowe
        </h2>
      </div>
      <div class="border-t border-gray-200">
        <div class="flex-1 grid grid-cols-1 p-4 md:grid-cols-3 gap-4">
          <div class="relative">
            <div class="pointer-events-none absolute inset-y-0 left-0 pl-3 flex items-center">
              <MagnifyingGlassIcon class="size-5 text-gray-400" />
            </div>
            <input
              v-model.trim="form.search"
              type="search"
              class="block py-2 pr-3 pl-10 w-full max-w-lg placeholder:text-gray-500 focus:text-gray-900 focus:placeholder:text-gray-400 bg-white rounded-md border border-gray-300 focus:border-blumilk-500 focus:outline-none focus:ring-1 focus:ring-blumilk-500 sm:text-sm"
              placeholder="Szukaj"
            >
          </div>
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
                Numer
              </th>
              <th
                scope="col"
                class="py-3 px-4 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase whitespace-nowrap"
              >
                Data utworzenia
              </th>
              <th scope="col" />
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-100">
            <tr
              v-for="benefitsReport in benefitsReports.data"
              :key="benefitsReport.id"
            >
              <td class="p-4 text-sm text-gray-500 whitespace-nowrap">
                <InertiaLink
                  :href="`/benefits-reports/${benefitsReport.id}`"
                  class="font-semibold text-blumilk-600 hover:text-blumilk-500 hover:underline focus:outline-blumilk-500"
                >
                  {{ benefitsReport.name }}
                </InertiaLink>
              </td>
              <td class="p-4 text-sm text-gray-500 whitespace-nowrap">
                {{ benefitsReport.createdAt }}
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
                      <div class="py-1">
                        <MenuItem
                          v-slot="{ active }"
                          class="flex"
                        >
                          <InertiaLink
                            as="button"
                            method="delete"
                            preserve-scroll
                            :href="`/benefits-reports/${benefitsReport.id}`"
                            :class="[active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'block w-full text-left font-medium px-4 py-2 text-sm']"
                          >
                            <TrashIcon class="mr-2 size-5 text-red-500" />
                            Usuń
                          </InertiaLink>
                        </MenuItem>
                      </div>
                    </MenuItems>
                  </transition>
                </Menu>
              </td>
            </tr>
            <tr v-if="! benefitsReports.data.length">
              <td
                colspan="100%"
                class="py-4 text-xl leading-5 text-center text-gray-700"
              >
                <EmptyState>
                  <template #title>
                    Brak raportów
                  </template>
                  <template #text>
                    Nie znaleziono raportów o takiej nazwie
                  </template>
                </EmptyState>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <Pagination :pagination="benefitsReports.meta" />
    </div>
  </AppLayout>
</template>
