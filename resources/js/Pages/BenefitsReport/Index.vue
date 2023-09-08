<script setup>
import { ChevronRightIcon } from '@heroicons/vue/24/solid'
import Pagination from '@/Shared/Pagination.vue'
import EmptyState from '@/Shared/Feedbacks/EmptyState.vue'
import { reactive, watch } from 'vue'
import { MagnifyingGlassIcon } from '@heroicons/vue/24/outline'
import { debounce } from 'lodash'
import { Inertia } from '@inertiajs/inertia'

const props = defineProps({
  benefitsReports: Object,
  filters: Object,
})

const form = reactive({
  search: props.filters.search,
})

watch(form, debounce(() => {

  Inertia.get('/benefits-reports', {
    search: form.search,
  }, {
    preserveState: true,
    replace: true,
  })
}, 300))

</script>

<template>
  <InertiaHead title="Raporty benefitowe" />
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
            <MagnifyingGlassIcon class="w-5 h-5 text-gray-400" />
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
          <InertiaLink
            v-for="benefitsReport in benefitsReports.data"
            :key="benefitsReport.id"
            :href="`/benefits-reports/${benefitsReport.id}`"
            as="tr"
            class="relative hover:bg-blumilk-25"
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
            <td class="p-4 text-sm text-gray-500 whitespace-nowrap">
              <InertiaLink
                :href="`/benefits-reports/${benefitsReport.id}`"
                class="flex justify-around focus:outline-blumilk-500"
              >
                <ChevronRightIcon class="block w-6 h-6 fill-blumilk-500" />
              </InertiaLink>
            </td>
          </InertiaLink>
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
</template>
