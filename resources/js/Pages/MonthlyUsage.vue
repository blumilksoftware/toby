<script setup>
import { useMonthInfo } from '@/Composables/monthInfo.js'
import VacationBar from '@/Shared/VacationBar.vue'
import UserProfileLink from '@/Shared/UserProfileLink.vue'
import { ref, watch } from 'vue'
import { DateTime } from 'luxon'
import { Inertia } from '@inertiajs/inertia'
import YearPicker from '@/Shared/Forms/YearPicker.vue'

const props = defineProps({
  year: Number,
  monthlyUsage: Object,
  currentMonth: String,
})

const selectedYear = ref(props.year)
const currentDate = DateTime.now()

const { getMonths } = useMonthInfo()
const months = getMonths()

function isCurrentMonth(month) {
  return (props.year === currentDate.year && props.currentMonth === month.value)
}

watch(selectedYear, (value, oldValue) => {
  if (value === oldValue)
    return

  Inertia.visit('/vacation/monthly-usage', {
    data: { year: value },
  })
})
</script>

<template>
  <InertiaHead title="Wykorzystanie miesięczne urlopu" />
  <div class="bg-white shadow-md">
    <div class="flex justify-between items-center p-4 sm:px-6">
      <div class="flex items-center">
        <h2 class="text-lg font-medium leading-6 text-gray-900">
          Wykorzystanie miesięczne urlopu w roku
          <YearPicker
            v-model="selectedYear"
            :from="currentDate.year + 1"
            :to="currentDate.year - 20"
            class="inline-block ml-2"
          />
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
                :class="{'bg-blumilk-50': isCurrentMonth(month)}"
                class="py-3 px-6 text-xs font-semibold tracking-wider text-center text-gray-500 uppercase"
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
              :class="[item.user.isActive ? '' : 'bg-gray-100', 'hover:bg-blumilk-25']"
            >
              <th class="p-4 text-sm font-semibold text-gray-500 capitalize whitespace-nowrap">
                <UserProfileLink
                  :user="item.user"
                >
                  <div class="flex justify-start items-center">
                    <span class="inline-flex justify-center items-center w-10 h-10 rounded-full">
                      <img
                        :src="item.user.avatar"
                        class="w-10 h-10 rounded-full"
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
                </UserProfileLink>
              </th>
              <td
                v-for="month in months"
                :key="month.value"
                :class="{'bg-blumilk-25': isCurrentMonth(month)}"
                class="p-4 text-sm font-semibold text-center text-gray-500"
              >
                {{ item.months[month.value] ?? '-' }}
              </td>
              <td class="p-4 text-sm font-semibold text-center text-gray-500">
                <div style="min-width: 300px;">
                  <VacationBar
                    :stats="{ used: item.stats.used, pending: item.stats.pending, remaining: item.stats.remaining }"
                  />
                </div>
              </td>
            </tr>
            <tr v-if="!monthlyUsage.length">
              <td
                class="py-4 text-xl leading-5 text-center text-gray-700"
                colspan="100%"
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
