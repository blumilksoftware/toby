<template>
  <InertiaHead title="Wykorzystanie miesięczne urlopu" />
  <div class="bg-white shadow-md">
    <div class="flex justify-between items-center p-4 sm:px-6">
      <div class="flex items-center">
        <h2 class="text-lg leading-6 font-medium text-gray-900">
          Wykorzystanie miesięczne urlopu wypoczynkowego
        </h2>
      </div>
    </div>
    <div class="border-t border-gray-200">
      <div class="overflow-x-auto xl:overflow-x-visible overflow-y-hidden">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="w-64 px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider text-left">
                Pracownik
              </th>
              <th
                v-for="month in months"
                :key="month"
                class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider text-center"
                :class="{'bg-blumilk-50': isCurrentMonth(month)}"
                style="min-width: 46px;"
              >
                <span :class="{'text-blumilk-600': isCurrentMonth(month)}">
                  {{ month.shortcut }}
                </span>
              </th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">
                Wykorzystanie urlopu
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-100">
            <tr
              v-for="item in monthlyUsage"
              :key="item.user.id"
              class="hover:bg-blumilk-25"
            >
              <th class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 font-semibold capitalize">
                <div class="flex justify-start items-center">
                  <span class="inline-flex items-center justify-center h-10 w-10 rounded-full">
                    <img
                      class="h-10 w-10 rounded-full"
                      :src="item.user.avatar"
                    >
                  </span>
                  <div class="ml-3">
                    <div
                      class="text-sm font-medium text-gray-900 whitespace-nowrap"
                    >
                      {{ item.user.name }}
                    </div>
                  </div>
                </div>
              </th>
              <td
                v-for="month in months"
                :key="month.value"
                class="px-4 py-4 text-sm text-gray-500 font-semibold text-center"
                :class="{'bg-blumilk-25': isCurrentMonth(month)}"
              >
                {{ item.months[month.value] ?? '-' }}
              </td>
              <td class="px-4 py-4 text-sm text-gray-500 font-semibold text-center">
                <div style="min-width: 300px;">
                  <VacationBar :stats="{ used: item.stats.used, pending: item.stats.pending, remaining: item.stats.remaining }" />
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useMonthInfo } from '@/Composables/monthInfo'
import VacationBar from '@/Shared/VacationBar'

const props = defineProps({
  years: Object,
  monthlyUsage: Object,
  currentMonth: String,
})

const { getMonths } = useMonthInfo()
const months = getMonths()

function isCurrentMonth(month) {
  return (props.years.selected.year === props.years.current.year && props.currentMonth === month.value)
}
</script>
