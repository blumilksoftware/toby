<script setup>
import EmptyState from '@/Shared/Feedbacks/EmptyState.vue'
import Pagination from '@/Shared/Pagination.vue'
import { MagnifyingGlassIcon } from '@heroicons/vue/24/outline'
import { reactive, watch } from 'vue'
import { debounce } from 'lodash'
import { router } from '@inertiajs/vue3'
import MultipleCombobox from '@/Shared/Forms/MultipleCombobox.vue'
import {
  CheckIcon,
  XMarkIcon,
  ComputerDesktopIcon,
} from '@heroicons/vue/24/solid'

const props = defineProps({
  equipmentItems: Object,
  labels: Array,
  filters: Object,
})

const form = reactive({
  search: props.filters.search,
  labels: props.filters.labels ?? [],
})

watch(form, debounce(() => {
  router.get('/equipment-items', {
    search: form.search,
    labels: form.labels,
  }, {
    preserveState: true,
    replace: true,
  })
}, 300))
</script>

<template>
  <AppLayout>
    <template #title>Sprzęt</template>
    <div class="bg-white shadow-md">
      <div class="flex justify-between items-center p-4 sm:px-6">
        <div>
          <h2 class="text-lg font-medium leading-6 text-gray-900">
            Sprzęt
          </h2>
        </div>
      </div>
      <div class="border-t border-gray-200">
        <div class="items-end grid grid-cols-1 p-4 md:grid-cols-3 gap-4">
          <div class="relative">
            <div class="pointer-events-none absolute inset-y-0 left-0 pl-3 flex items-center">
              <MagnifyingGlassIcon class="size-5 text-gray-400" />
            </div>
            <input
              v-model.trim="form.search"
              type="search"
              class="inline-block align-baseline py-2 pr-3 pl-10 w-full placeholder:text-gray-500 focus:text-gray-900 focus:placeholder:text-gray-400 bg-white rounded-md border border-gray-300 focus:border-blumilk-500 focus:outline-none focus:ring-1 focus:ring-blumilk-500 sm:text-sm"
              placeholder="Szukaj"
            >
          </div>
          <MultipleCombobox
            v-model="form.labels"
            placeholder="Etykiety"
            :items="labels"
          />
        </div>
        <div class="overflow-auto xl:overflow-visible">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th
                  scope="col"
                  class="py-3 px-4 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase whitespace-nowrap"
                >
                  ID
                </th>
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
                  Etykiety
                </th>
                <th
                  scope="col"
                  class="py-3 px-4 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase whitespace-nowrap"
                >
                  Czy mobilny?
                </th>
                <th
                  scope="col"
                  class="py-3 px-4 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase whitespace-nowrap"
                >
                  Od kiedy
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
              <tr
                v-for="item in equipmentItems.data"
                :key="item.id"
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
                    <CheckIcon class="size-6 text-green-500" />
                  </div>
                  <div v-else>
                    <XMarkIcon class="size-6 text-red-500" />
                  </div>
                </td>
                <td class="p-4 text-sm text-gray-500 whitespace-nowrap">
                  {{ item.displayAssignedAt || '-' }}
                </td>
              </tr>
              <tr v-if="!equipmentItems.data.length">
                <td
                  colspan="100%"
                  class="py-4 text-xl leading-5 text-center text-gray-700"
                >
                  <EmptyState class="text-gray-700">
                    <template #head>
                      <ComputerDesktopIcon class="mx-auto size-12" />
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
  </AppLayout>
</template>
