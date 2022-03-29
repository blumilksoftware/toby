<template>
  <InertiaHead title="Kalendarz urlopów" />
  <div class="bg-white shadow-md">
    <div class="flex justify-between items-center p-4 sm:px-6">
      <div class="flex items-center">
        <h2 class="text-lg leading-6 font-medium text-gray-900">
          Kalendarz urlopów
        </h2>
        <div class="ml-5 flex items-center rounded-md shadow-sm md:items-stretch">
          <InertiaLink
            v-if="previousMonth"
            as="button"
            :href="`/vacation/calendar/${previousMonth.value}`"
            class="flex items-center justify-center rounded-l-md border border-r-0 border-gray-300 bg-white py-2 pl-3 pr-4 text-gray-400 hover:text-gray-500 focus:relative md:w-9 md:px-2 md:hover:bg-gray-50"
          >
            <ChevronLeftIcon class="h-5 w-5" />
          </InertiaLink>
          <span
            v-else
            class="flex items-center justify-center rounded-l-md border border-r-0 border-gray-300 bg-white py-2 pl-3 pr-4 text-gray-400 hover:text-gray-500 focus:relative md:w-9 md:px-2 md:hover:bg-gray-50"
          >
            <ChevronLeftIcon class="h-5 w-5" />
          </span>
          <InertiaLink
            as="button"
            :href="`/vacation/calendar/${currentMonth.value}`"
            class="hidden border-t border-b border-gray-300 bg-white px-3.5 text-sm font-medium flex items-center text-gray-700 hover:bg-gray-50 hover:text-gray-900 focus:relative md:block"
          >
            Dzisiaj
          </InertiaLink>
          <InertiaLink
            v-if="nextMonth"
            as="button"
            :href="`/vacation/calendar/${nextMonth.value}`"
            class="flex items-center justify-center rounded-r-md border border-l-0 border-gray-300 bg-white py-2 pl-4 pr-3 text-gray-400 hover:text-gray-500 focus:relative md:w-9 md:px-2 md:hover:bg-gray-50"
          >
            <ChevronRightIcon class="h-5 w-5" />
          </InertiaLink>
          <span
            v-else
            class="flex items-center justify-center rounded-r-md border border-l-0 border-gray-300 bg-white py-2 pl-4 pr-3 text-gray-400 hover:text-gray-500 focus:relative md:w-9 md:px-2 md:hover:bg-gray-50"
          >
            <ChevronRightIcon class="h-5 w-5" />
          </span>
        </div>
      </div>
      <div v-if="can.generateTimesheet">
        <a
          :href="`/vacation/timesheet/${selectedMonth.value}`"
          class="inline-flex items-center px-4 py-3 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-blumilk-600 hover:bg-blumilk-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blumilk-500"
        >
          Pobierz plik Excel
        </a>
      </div>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-center text-sm border border-gray-300">
        <thead>
          <tr>
            <th class="w-64 py-2 border text-lg font-semibold text-gray-800 border-gray-300">
              <div class="flex justify-center items-center">
                {{ selectedMonth.name }} {{ years.selected.year }}
              </div>
            </th>
            <th
              v-for="day in calendar"
              :key="day.dayOfMonth"
              class="border border-gray-300 text-lg font-semibold text-gray-900 py-2 px-2"
              style="min-width: 46px;"
              :class="{ 'bg-red-100 text-red-800': day.isWeekend || day.isHoliday, 'text-blumilk-600 bg-blumilk-25': day.isToday }"
            >
              <div>
                {{ day.dayOfMonth }}
                <p class="font-normal mt-1 text-sm capitalize">
                  {{ day.dayOfWeek }}
                </p>
              </div>
            </th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="user in users.data"
            :key="user.id"
          >
            <th class="border border-gray-300 py-2 px-2">
              <div class="flex justify-start items-center">
                <span class="inline-flex items-center justify-center h-8 w-8 rounded-full">
                  <img :src="user.avatar">
                </span>
                <div class="ml-3">
                  <div class="text-sm font-medium text-gray-900 truncate">
                    {{ user.name }}
                  </div>
                </div>
              </div>
            </th>
            <td
              v-for="day in calendar"
              :key="day.dayOfMonth"
              class="border border-gray-300"
              :class="{ 'bg-blumilk-25': day.isToday, 'bg-red-100': day.isWeekend || day.isHoliday}"
            >
              <div
                v-if="day.vacations.includes(user.id)"
                class="flex justify-center items-center"
              >
                <VacationTypeCalendarIcon :type="day.vacationTypes[user.id]" />
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ChevronLeftIcon, ChevronRightIcon } from '@heroicons/vue/solid'
import { computed } from 'vue'
import { useMonthInfo } from '@/Composables/monthInfo'
import VacationTypeCalendarIcon from '@/Shared/VacationTypeCalendarIcon'

const props = defineProps({
  users: Object,
  auth: Object,
  calendar: Object,
  current: String,
  selected: String,
  years: Object,
  can: Object,
})

const { getMonths, findMonth } = useMonthInfo()

const months = getMonths()

const currentMonth = computed(() => findMonth(props.current))
const selectedMonth = computed(() => findMonth(props.selected))
const previousMonth = computed(() => months[months.indexOf(selectedMonth.value) - 1])
const nextMonth = computed(() => months[months.indexOf(selectedMonth.value) + 1])
</script>
