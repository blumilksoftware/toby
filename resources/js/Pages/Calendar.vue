<script setup>
import { ChevronLeftIcon, ChevronRightIcon, ChevronDoubleRightIcon, ChevronDoubleLeftIcon, ChevronUpDownIcon, ArrowUturnLeftIcon } from '@heroicons/vue/24/solid'
import { computed, ref, watch } from 'vue'
import { useMonthInfo } from '@/Composables/monthInfo.js'
import VacationTypeCalendarIcon from '@/Shared/VacationTypeCalendarIcon.vue'
import CalendarDay from '@/Shared/CalendarDay.vue'
import UserProfileLink from '@/Shared/UserProfileLink.vue'
import { Listbox, ListboxButton, ListboxOption, ListboxOptions } from '@headlessui/vue'
import { useForm } from '@inertiajs/inertia-vue3'
import { Inertia } from '@inertiajs/inertia'

const props = defineProps({
  users: Object,
  auth: Object,
  calendar: Object,
  currentMonth: String,
  currentYear: Number,
  selectedMonth: String,
  selectedYear: Number,
  years: Object,
  previousYearPeriod: Object,
  nextYearPeriod: Object,
})

let activeElement = ref(undefined)

const { getMonths, findMonth } = useMonthInfo()

const months = getMonths()

const form = useForm({
  selectedMonth: months.find(month => month.value === props.selectedMonth),
})

watch(() => form.selectedMonth, (value) => {
  if (value) {
    Inertia.visit(`/calendar/${value.value}`)
  }
})

const selectedMonth = computed(() => findMonth(props.selectedMonth))
const previousMonth = computed(() => months[months.indexOf(selectedMonth.value) - 1])
const nextMonth = computed(() => months[months.indexOf(selectedMonth.value) + 1])

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
  return props.auth.can.createRequestsOnBehalfOfEmployee ? { user: user.id, from_date: day.date } : { from_date: day.date }
}

function linkVacationRequest(user) {
  return props.auth.user.id === user.id || props.auth.can.manageRequestsAsTechnicalApprover || props.auth.can.manageRequestsAsAdministrativeApprover
}
</script>

<template>
  <InertiaHead title="Kalendarz" />
  <div class="bg-white shadow-md">
    <div class="flex-row sm:flex justify-between items-center p-4 sm:px-6">
      <div class="flex-row sm:flex items-center">
        <h2 class="text-lg font-medium leading-6 sm:text-center mb-2 sm:mb-0 text-gray-900">
          Kalendarz
        </h2>
        <div class="flex items-center sm:ml-3 md:items-stretch">
          <InertiaLink
            v-if="previousMonth"
            :href="`/calendar/${previousMonth.value}`"
            as="button"
            class="flex focus:relative justify-center items-center p-2 text-gray-400 hover:text-gray-500 bg-white rounded-l-md border border-r-0 border-gray-300 md:px-2 md:w-9 md:hover:bg-gray-50"
          >
            <ChevronLeftIcon class="w-5 h-5" />
          </InertiaLink>
          <InertiaLink
            v-else-if="previousYearPeriod"
            :href="`/calendar/${months[11].value}/${previousYearPeriod.year}`"
            as="button"
            class="flex focus:relative justify-center items-center p-2 text-gray-400 hover:text-gray-500 bg-white rounded-l-md border border-r-0 border-gray-300 md:px-2 md:w-9 md:hover:bg-gray-50"
          >
            <ChevronDoubleLeftIcon class="w-5 h-5" />
          </InertiaLink>
          <span
            v-else
            class="flex justify-center items-center text-gray-400 bg-gray-100 rounded-l-md border border-r-0 border-gray-300 md:px-2 md:w-9"
          >
            <ChevronDoubleLeftIcon class="w-5 h-5" />
          </span>
          <Listbox
            v-model="form.selectedMonth"
            as="div"
            class="items-center grid-cols-3 w-[135px] h-[] text-sm font-medium text-gray-700 hover:text-gray-900 bg-white hover:bg-gray-50 border-y border-gray-300 focus:outline-blumilk-500"
          >
            <div class="relative sm:col-span-2 sm:mt-0">
              <ListboxButton
                class="relative pr-10 pl-3 w-full h-[36px] max-w-lg text-left bg-white focus:outline-none shadow-sm sm:text-sm cursor-pointer"
              >
                <template v-if="form.selectedMonth">
                  <span class="block truncate text-center">
                    {{ form.selectedMonth.name }}
                  </span>
                  <span class="flex absolute inset-y-0 right-0 items-center pr-2 pointer-events-none">
                    <ChevronUpDownIcon class="w-5 h-5 text-gray-400" />
                  </span>
                </template>
              </ListboxButton>
              <transition
                leave-active-class="transition ease-in duration-100"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
              >
                <ListboxOptions
                  class="overflow-auto absolute z-10 py-1 mt-1 w-auto max-w-lg max-h-60 text-base bg-white rounded-md focus:outline-none ring-1 ring-black ring-opacity-5 shadow-lg sm:text-sm"
                >
                  <ListboxOption
                    v-for="month in months"
                    :key="month.value"
                    v-slot="{ active, selected }"
                    :value="month"
                    as="template"
                  >
                    <li :class="[active ? 'bg-gray-100' : 'text-gray-900', 'cursor-default select-none relative py-2 pl-3 pr-9 cursor-pointer']">
                      <span :class="[selected ? 'font-semibold' : 'font-normal', 'block truncate']">
                        {{ month.name }}
                      </span>
                    </li>
                  </ListboxOption>
                </ListboxOptions>
              </transition>
            </div>
          </Listbox>
          <InertiaLink
            v-if="nextMonth"
            :href="`/calendar/${nextMonth.value}`"
            as="button"
            class="flex focus:relative justify-center items-center p-2 text-gray-400 hover:text-gray-500 bg-white rounded-r-md border border-l-0 border-gray-300 focus:outline-blumilk-500 md:px-2 md:w-9 md:hover:bg-gray-50"
          >
            <ChevronRightIcon class="w-5 h-5" />
          </InertiaLink>
          <InertiaLink
            v-else-if="nextYearPeriod"
            :href="`/calendar/${months[0].value}/${nextYearPeriod.year}`"
            as="button"
            class="flex focus:relative justify-center items-center p-2 text-gray-400 hover:text-gray-500 bg-white rounded-r-md border border-l-0 border-gray-300 focus:outline-blumilk-500 md:px-2 md:w-9 md:hover:bg-gray-50"
          >
            <ChevronDoubleRightIcon class="w-5 h-5" />
          </InertiaLink>
          <span
            v-else
            class="flex justify-center items-center text-gray-400 bg-gray-100 rounded-r-md border border-l-0 border-gray-300 md:px-2 md:w-9"
          >
            <ChevronDoubleRightIcon class="w-5 h-5" />
          </span>
          <InertiaLink
            v-if="selectedMonth.value !== currentMonth || currentYear !== selectedYear"
            :href="`/calendar/${props.currentMonth}/${props.currentYear}`"
            as="button"
            class="flex focus:relative justify-center items-center p-2 bg-white md:px-2 md:hover:bg-gray-50 ml-1"
          >
            <ArrowUturnLeftIcon class="w-5 h-5 text-blumilk-600 hover:text-blumilk-500" />
            <span class="ml-1.5 text-sm font-semibold text-blumilk-600 hover:text-blumilk-500">
              Dzisiaj
            </span>
          </InertiaLink>
        </div>
      </div>
      <div
        class="flex mt-3 sm:mt-0"
      >
        <a
          v-if="auth.can.manageRequestsAsAdministrativeApprover"
          :href="`/vacation/timesheet/${selectedMonth.value}`"
          class="block py-3 px-4 sm:ml-3 text-sm font-medium leading-4 text-center text-white bg-blumilk-600 hover:bg-blumilk-700 rounded-md border border-transparent focus:outline-none focus:ring-2 focus:ring-blumilk-500 focus:ring-offset-2 shadow-sm"
        >
          Pobierz plik Excel
        </a>
        <a
          v-if="auth.can.manageOvertimeAsAdministrativeApprover"
          :href="`/overtime/timesheet/${selectedMonth.value}`"
          class="block py-3 px-4 ml-3 text-sm font-medium leading-4 text-center text-white bg-blumilk-600 hover:bg-blumilk-700 rounded-md border border-transparent focus:outline-none focus:ring-2 focus:ring-blumilk-500 focus:ring-offset-2 shadow-sm"
        >
          Pobierz nadgodziny
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
                  <span class="inline-flex justify-center items-center w-8 h-8 rounded-full">
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
</template>
