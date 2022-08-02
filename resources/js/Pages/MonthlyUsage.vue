<template>
  <InertiaHead title="Wykorzystanie miesięczne urlopu" />
  <div class="bg-white shadow-md">
    <div class="flex justify-between items-center p-4 sm:px-6">
      <div class="flex items-center">
        <h2 class="text-lg font-medium leading-6 text-gray-900">
          Wykorzystanie miesięczne urlopu
        </h2>
      </div>
    </div>
    <div class="border-t border-gray-200">
      <div class="overflow-x-auto overflow-y-hidden xl:overflow-x-visible">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="py-3 px-6 w-64 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase">
                Pracownik
              </th>
              <th
                v-for="month in months"
                :key="month"
                class="py-3 px-6 text-xs font-semibold tracking-wider text-center text-gray-500 uppercase"
                :class="{'bg-blumilk-50': isCurrentMonth(month)}"
                style="min-width: 46px;"
              >
                <span :class="{'text-blumilk-600': isCurrentMonth(month)}">
                  {{ month.shortcut }}
                </span>
              </th>
              <th class="py-3 px-6 text-xs font-semibold tracking-wider text-center text-gray-500 uppercase">
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
              <th class="p-4 text-sm font-semibold text-gray-500 capitalize whitespace-nowrap">
                <div class="flex justify-start items-center">
                  <span class="inline-flex justify-center items-center w-10 h-10 rounded-full">
                    <img
                      class="w-10 h-10 rounded-full"
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
                class="p-4 text-sm font-semibold text-center text-gray-500"
                :class="{'bg-blumilk-25': isCurrentMonth(month)}"
              >
                {{ item.months[month.value] ?? '-' }}
              </td>
              <td class="p-4 text-sm font-semibold text-center text-gray-500">
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
import { useMonthInfo } from '@/Composables/monthInfo.js'
import VacationBar from '@/Shared/VacationBar.vue'

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
