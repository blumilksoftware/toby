<script setup>
import { ArrowUturnLeftIcon, ChevronLeftIcon, ChevronRightIcon } from '@heroicons/vue/24/solid'
import { ref, watch } from 'vue'
import VacationTypeCalendarIcon from '@/Shared/VacationTypeCalendarIcon.vue'
import CalendarDay from '@/Shared/CalendarDay.vue'
import UserProfileLink from '@/Shared/UserProfileLink.vue'
import { DateTime } from 'luxon'
import MonthPicker from '@/Shared/Forms/MonthPicker.vue'
import { router } from '@inertiajs/vue3'
import InertiaLink from '@/Shared/InertiaLink.vue'
import { useGlobalProps } from '@/Composables/useGlobalProps'
import AppLayout from '@/Shared/Layout/AppLayout.vue'

const props = defineProps({
  users: Object,
  calendar: Object,
  workingHours: Number,
  selectedDate: String,
})

const { auth } = useGlobalProps()

let activeElement = ref(undefined)

const currentDate = DateTime.now()

const selectedMonth = ref(DateTime.fromISO(props.selectedDate))

watch(selectedMonth, (value, oldValue) => {
  if (!value || value.toFormat('LL-YYY') === oldValue?.toFormat('LL-YYY')) {
    return
  }

  router.visit(`/calendar/${value.toFormat('LL-yyyy')}`)
})

function previousMonth() {
  selectedMonth.value = selectedMonth.value.minus({ month: 1 })
}

function nextMonth() {
  selectedMonth.value = selectedMonth.value.plus({ month: 1 })
}

function currentMonth() {
  selectedMonth.value = currentDate
}

function isActiveDay(key) {
  return activeElement.value === key
}

function setActiveDay(key) {
  if (activeElement.value === undefined)
    activeElement.value = key
}

function unsetActiveDay() {
  activeElement.value = undefined
}

function linkParameters(user, day) {
  return auth.value.can.createRequestsOnBehalfOfEmployee ? { user: user.id, from_date: day.date } : { from_date: day.date }
}

function linkVacationRequest(user) {
  return auth.value.user.id === user.id || auth.value.can.manageRequestsAsTechnicalApprover || auth.value.can.manageRequestsAsAdministrativeApprover
}
</script>

<template>
  <AppLayout>
    <template #title>Kalendarz</template>
    <div class="bg-white shadow-md">
      <div class="flex-row sm:flex justify-between items-center p-4 sm:px-6">
        <div class="flex-row sm:flex items-center">
          <h2 class="text-lg font-medium leading-6 sm:text-center mb-2 sm:mb-0 text-gray-900">
            Kalendarz
          </h2>
          <div class="flex items-center sm:ml-3 md:items-stretch">
            <button
              type="button"
              class="flex focus:relative justify-center items-center p-2 text-gray-400 hover:text-gray-500 bg-white rounded-l-md border border-r-0 border-gray-300 md:px-2 md:w-9 md:hover:bg-gray-50"
              @click="previousMonth()"
            >
              <ChevronLeftIcon class="size-5" />
            </button>
            <MonthPicker
              :model-value="selectedMonth.toFormat('LL-yyyy')"
              class="block w-40 shadow-sm disabled:bg-gray-100 focus:ring-blumilk-500 focus:border-blumilk-500 sm:text-sm border-gray-300"
              @update:model-value="selectedMonth = DateTime.fromFormat($event, 'LL/yyyy')"
            />
            <button
              type="button"
              class="flex focus:relative justify-center items-center p-2 text-gray-400 hover:text-gray-500 bg-white rounded-r-md border border-l-0 border-gray-300 focus:outline-blumilk-500 md:px-2 md:w-9 md:hover:bg-gray-50"
              @click="nextMonth()"
            >
              <ChevronRightIcon class="size-5" />
            </button>
            <button
              v-if="selectedMonth.toFormat('LL-yyyy') !== currentDate.toFormat('LL-yyyy')"
              type="button"
              class="flex focus:relative justify-center items-center p-2 bg-white md:px-2 md:hover:bg-gray-50 ml-1"
              @click="currentMonth()"
            >
              <ArrowUturnLeftIcon class="size-5 text-blumilk-600 hover:text-blumilk-500" />
              <span class="ml-1.5 text-sm font-semibold text-blumilk-600 hover:text-blumilk-500">
                Dzisiaj
              </span>
            </button>
          </div>
        </div>
        <div class="flex-row">
          <div
            class="flex items-center justify-end gap-3 mt-3 sm:mt-0"
          >
            <a
              v-if="auth.can.manageRequestsAsAdministrativeApprover"
              :href="`/vacation/timesheet/${selectedMonth.toFormat('LL-yyyy')}`"
              class="block py-3 px-4 text-sm font-medium leading-4 text-center text-white bg-blumilk-600 hover:bg-blumilk-700 rounded-md border border-transparent focus:outline-none focus:ring-2 focus:ring-blumilk-500 focus:ring-offset-2 shadow-sm"
            >
              Pobierz plik Excel
            </a>
            <a
              v-if="auth.can.manageOvertimeAsAdministrativeApprover"
              :href="`/overtime/timesheet/${selectedMonth.toFormat('LL-yyyy')}`"
              class="block py-3 px-4 text-sm font-medium leading-4 text-center text-white bg-blumilk-600 hover:bg-blumilk-700 rounded-md border border-transparent focus:outline-none focus:ring-2 focus:ring-blumilk-500 focus:ring-offset-2 shadow-sm"
            >
              Pobierz nadgodziny
            </a>
          </div>
          <div
            v-if="workingHours !== null"
            class="flex items-center justify-start sm:justify-end mt-3 sm:mt-1"
          >
            <p>
              Liczba przepracowanych godzin: <span class="font-bold">{{ workingHours }}</span>
            </p>
          </div>
        </div>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full text-sm text-center border border-gray-300">
          <thead>
            <tr>
              <th class="py-2 w-64 text-lg font-semibold text-gray-800 border border-gray-300">
                <div class="flex justify-center items-center capitalize">
                  {{ selectedMonth.toLocaleString({ month: 'long', year: 'numeric' }) }}
                </div>
              </th>
              <th
                v-for="day in calendar"
                :key="day.dayOfMonth"
                :class="{ 'bg-red-100 text-red-800': day.isWeekend || day.isHoliday, 'text-blumilk-600 bg-blumilk-25': day.isToday }"
                class="p-2 text-lg font-semibold text-gray-900 border border-gray-300"
                style="min-width: 46px;"
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
              :class="[user.isActive ? '' : 'bg-gray-100']"
            >
              <th class="p-2 border border-gray-300">
                <UserProfileLink
                  :user="user"
                >
                  <div class="flex justify-start items-center">
                    <span class="inline-flex justify-center items-center size-8 rounded-full">
                      <img :src="user.avatar">
                    </span>
                    <div class="ml-3">
                      <div class="text-sm font-medium text-gray-900 truncate">
                        {{ user.name }}
                      </div>
                    </div>
                  </div>
                </UserProfileLink>
              </th>
              <td
                v-for="day in calendar"
                :key="day.dayOfMonth"
                :class="{ 'bg-blumilk-25': day.isToday, 'bg-red-100': day.isWeekend || day.isHoliday }"
                class="border border-gray-300"
                @mouseleave="unsetActiveDay"
                @mouseover="setActiveDay(user.id + '+' + day.date)"
              >
                <div
                  v-if="user.id in day.vacations"
                  class="flex justify-center items-center"
                >
                  <CalendarDay
                    :see-vacation-details="linkVacationRequest(user)"
                    :vacation="day.vacations[user.id]"
                  />
                </div>
                <template
                  v-else-if="isActiveDay(user.id + '+' + day.date) && !day.isWeekend && !day.isHoliday && (auth.user.id === user.id || auth.can.createRequestsOnBehalfOfEmployee)"
                >
                  <InertiaLink
                    :data="linkParameters(user, day)"
                    href="/vacation/requests/create"
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
  </AppLayout>
</template>
