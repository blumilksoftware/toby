<script setup>
import { ChevronLeftIcon, ChevronRightIcon } from '@heroicons/vue/24/solid'
import { computed, ref } from 'vue'
import { useMonthInfo } from '@/Composables/monthInfo.js'
import VacationTypeCalendarIcon from '@/Shared/VacationTypeCalendarIcon.vue'
import VacationPopup from '@/Shared/VacationPopup.vue'
import CalendarDay from '@/Shared/CalendarDay.vue'

const props = defineProps({
  users: Object,
  auth: Object,
  calendar: Object,
  current: String,
  selected: String,
  years: Object,
})

let activeElement = ref(undefined)

const { getMonths, findMonth } = useMonthInfo()

const months = getMonths()

const currentMonth = computed(() => findMonth(props.current))
const selectedMonth = computed(() => findMonth(props.selected))
const previousMonth = computed(() => months[months.indexOf(selectedMonth.value) - 1])
const nextMonth = computed(() => months[months.indexOf(selectedMonth.value) + 1])

function isActiveDay(key) {
  return activeElement.value === key
}

function setActiveDay(key) {
  if(activeElement.value === undefined)
    activeElement.value = key
}

function unsetActiveDay() {
  activeElement.value = undefined
}

function linkParameters(user, day) {
  return props.auth.can.createRequestsOnBehalfOfEmployee ? { user: user.id, from_date: day.date } : { from_date: day.date }
}

function linkVacationRequest(user){
  return props.auth.user.id === user.id || props.auth.can.manageRequestsAsTechnicalApprover || props.auth.can.manageRequestsAsAdministrativeApprover
}
</script>

<template>
  <InertiaHead title="Kalendarz" />
  <div class="bg-white shadow-md">
    <div class="flex justify-between items-center p-4 sm:px-6">
      <div class="flex items-center">
        <h2 class="text-lg font-medium leading-6 text-center text-gray-900">
          Kalendarz
        </h2>
        <div class="flex items-center ml-3 rounded-md shadow-sm md:items-stretch">
          <InertiaLink
            v-if="previousMonth"
            as="button"
            :href="`/calendar/${previousMonth.value}`"
            class="flex focus:relative justify-center items-center p-2 text-gray-400 hover:text-gray-500 bg-white rounded-l-md border border-r-0 border-gray-300 focus:outline-blumilk-500 md:px-2 md:w-9 md:hover:bg-gray-50"
          >
            <ChevronLeftIcon class="w-5 h-5" />
          </InertiaLink>
          <span
            v-else
            class="flex justify-center items-center p-2 text-gray-400 bg-gray-100 rounded-l-md border border-r-0 border-gray-300 md:px-2 md:w-9"
          >
            <ChevronLeftIcon class="w-5 h-5" />
          </span>
          <InertiaLink
            v-if="years.current.year === years.selected.year"
            as="button"
            :href="`/calendar/${currentMonth.value}`"
            class="hidden focus:relative items-center p-2 text-sm font-medium text-gray-700 hover:text-gray-900 bg-white hover:bg-gray-50 border-y border-gray-300 focus:outline-blumilk-500 md:flex"
          >
            Dzisiaj
          </InertiaLink>
          <InertiaLink
            v-if="nextMonth"
            as="button"
            :href="`/calendar/${nextMonth.value}`"
            class="flex focus:relative justify-center items-center p-2 text-gray-400 hover:text-gray-500 bg-white rounded-r-md border border-l-0 border-gray-300 focus:outline-blumilk-500 md:px-2 md:w-9 md:hover:bg-gray-50"
          >
            <ChevronRightIcon class="w-5 h-5" />
          </InertiaLink>
          <span
            v-else
            class="flex justify-center items-center p-2 text-gray-400 bg-gray-100 rounded-r-md border border-l-0 border-gray-300 md:px-2 md:w-9"
          >
            <ChevronRightIcon class="w-5 h-5" />
          </span>
        </div>
      </div>
      <div v-if="auth.can.manageRequestsAsAdministrativeApprover">
        <a
          :href="`/vacation/timesheet/${selectedMonth.value}`"
          class="block py-3 px-4 ml-3 text-sm font-medium leading-4 text-center text-white bg-blumilk-600 hover:bg-blumilk-700 rounded-md border border-transparent focus:outline-none focus:ring-2 focus:ring-blumilk-500 focus:ring-offset-2 shadow-sm"
        >
          Pobierz plik Excel
        </a>
      </div>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-sm text-center border border-gray-300">
        <thead>
          <tr>
            <th class="py-2 w-64 text-lg font-semibold text-gray-800 border border-gray-300">
              <div class="flex justify-center items-center">
                {{ selectedMonth.name }} {{ years.selected.year }}
              </div>
            </th>
            <th
              v-for="day in calendar"
              :key="day.dayOfMonth"
              class="p-2 text-lg font-semibold text-gray-900 border border-gray-300"
              style="min-width: 46px;"
              :class="{ 'bg-red-100 text-red-800': day.isWeekend || day.isHoliday, 'text-blumilk-600 bg-blumilk-25': day.isToday }"
            >
              <div>
                {{ day.dayOfMonth }}
                <p class="mt-1 text-sm font-normal capitalize">
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
            <th class="p-2 border border-gray-300">
              <div class="flex justify-start items-center">
                <span class="inline-flex justify-center items-center w-8 h-8 rounded-full">
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
              :class="{ 'bg-blumilk-25': day.isToday, 'bg-red-100': day.isWeekend || day.isHoliday }"
              @mouseover="setActiveDay(user.id + '+' + day.date)"
              @mouseleave="unsetActiveDay"
            >
              <div
                v-if="user.id in day.vacations"
                class="flex justify-center items-center"
              >
                <CalendarDay
                  :vacation="day.vacations[user.id]"
                  :see-vacation-details="linkVacationRequest(user)"
                />
              </div>
              <template
                v-else-if="isActiveDay(user.id + '+' + day.date) && !day.isWeekend && !day.isHoliday && (auth.user.id === user.id || auth.can.createRequestsOnBehalfOfEmployee)"
              >
                <InertiaLink
                  href="/vacation/requests/create"
                  :data="linkParameters(user, day)"
                >
                  <div class="flex justify-center items-center">
                    <VacationTypeCalendarIcon
                      type="create"
                    />
                  </div>
                </InertiaLink>
              </template>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
