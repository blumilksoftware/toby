<script setup>
import { CheckIcon, ChevronDownIcon, ChevronLeftIcon, ChevronRightIcon } from '@heroicons/vue/24/solid'
import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue'
import { computed, reactive, onMounted, ref } from 'vue'
import { DateTime } from 'luxon'
import useVacationTypeInfo from '@/Composables/vacationTypeInfo.js'
import { useMonthInfo } from '@/Composables/monthInfo.js'
import { viewModes, find as findViewMode } from '@/Shared/Widgets/Calendar/ViewModeOptions.js'
import DayComponent from '@/Shared/Widgets/Calendar/DayComponent.vue'
import { useStorage } from '@vueuse/core'
import axios from 'axios'

const props = defineProps({
  initHolidays: Object,
  initApprovedVacations: Object,
  initPendingVacations: Object,
})

const currentDate = DateTime.now()
const months = useMonthInfo().getMonths()
const { findType } = useVacationTypeInfo()

const calendar = reactive({
  days: [],
  viewMode: useStorage('calendarViewMode', 'week'),
  date: currentDate.startOf('week'),
})

const holidays = ref(new Map())
const approvedVacations = ref(new Map())
const pendingVacations = ref(new Map())
const loadedYears = ref([])

const calendarState = reactive({
  viewMode: {
    isWeek: computed(() => calendar.viewMode === 'week'),
    isMonth: computed(() => calendar.viewMode === 'month'),
    details: computed(() => findViewMode(calendar.viewMode)),
  },
  monthName: computed(() => months[calendar.date.month - 1]?.name),
  isPrevious: computed(() => calendar.date.minus({ [calendar.viewMode]: 1 }).year === currentDate.year),
  isNext: computed(() => calendar.date.plus({ [calendar.viewMode]: 1 }).year === currentDate.year),
})

onMounted(async () => initCalendar())

function initCalendar() {
  for (const [key, value] of Object.entries(props.initHolidays)) {
    holidays.value.set(key, value)
  }

  for (const [key, value] of Object.entries(props.initApprovedVacations)) {
    approvedVacations.value.set(key, value)
  }

  for (const [key, value] of Object.entries(props.initPendingVacations)) {
    pendingVacations.value.set(key, value)
  }

  loadedYears.value.push(currentDate.year)
  loadCalendar()
}

function loadCalendar() {
  let days = []

  const start = calendar.date.startOf(calendar.viewMode).startOf('week')
  const end = calendar.date.endOf(calendar.viewMode).endOf('week')

  for (let day = start; day < end; day = day.plus({ day: 1 })) {
    days.push(prepareDay(day))
  }

  calendar.days = days
}

async function loadYear(year) {
  const res = await axios.get(`/api/dashboard/calendar/${year}`)

  for (const [key, value] of Object.entries(res.data.holidays)) {
    holidays.value.set(key, value)
  }

  for (const [key, value] of Object.entries(res.data.approvedVacations)) {
    approvedVacations.value.set(key, value)
  }

  for (const [key, value] of Object.entries(res.data.pendingVacations)) {
    pendingVacations.value.set(key, value)
  }

  loadedYears.value.push(year)
}

function prepareDay(day) {
  const isCurrentMonth = isInCurrentMonth(day)
  const startDay = {
    date: day.toISODate(),
    isVacation: isCurrentMonth && isVacation(day),
    isPendingVacation: isCurrentMonth && isPendingVacation(day),
    isHoliday: isHoliday(day),
  }

  return {
    ...startDay,
    dayNumber: day.day,
    isCurrentMonth,
    isToday: isToday(day),
    isWeekend: isWeekend(day),
    getHolidayInfo: startDay.isHoliday ? getHolidayInfo(startDay) : undefined,
    getVacationType: startDay.isVacation || startDay.isPendingVacation ? getVacationType(startDay) : undefined,
    getVacationInfo: startDay.isVacation || startDay.isPendingVacation ? getVacationInfo(startDay) : undefined,
  }
}

async function today() {
  calendar.date = currentDate

  if (!loadedYears.value.includes(calendar.date.year)) {
    await loadYear(calendar.date.year)
  }

  loadCalendar()
}

async function previous() {
  calendar.date = calendar.date.minus({ [calendar.viewMode]: 1 })

  if (!loadedYears.value.includes(calendar.date.year)) {
    await loadYear(calendar.date.year)
  }

  loadCalendar()
}

async function next() {
  calendar.date = calendar.date.plus({ [calendar.viewMode]: 1 })

  if (!loadedYears.value.includes(calendar.date.year)) {
    await loadYear(calendar.date.year)
  }

  loadCalendar()
}

function updateViewMode(type) {
  calendar.viewMode = type
  loadCalendar()
}

function isInCurrentMonth(date) {
  return calendar.date.month === date.month
}

function isWeekend(date) {
  return date.weekday === 6 || date.weekday === 7
}

function isToday(date) {
  return date.toISODate() === DateTime.local().toISODate()
}

function isHoliday(date) {
  return holidays.value.has(date.toISODate())
}

function getHolidayInfo(day) {
  return holidays.value.get(day.date)
}

function isVacation(date) {
  return approvedVacations.value.has(date.toISODate())
}

function isPendingVacation(date) {
  return pendingVacations.value.has(date.toISODate())
}

function getVacationType(day) {
  return findType(getVacationInfo(day).type)
}

function getVacationInfo(day) {
  return day.isVacation ? approvedVacations.value.get(day.date) : pendingVacations.value.get(day.date)
}
</script>

<template>
  <section class="bg-white shadow-md">
    <div class="grid grid-cols-3 grid-rows-1 items-center justify-center p-4 sm:px-6">
      <h2 class="text-lg font-medium leading-6 text-gray-900">
        {{ calendarState.monthName }} {{ calendar.date.year }}
      </h2>
      <div class="flex justify-center">
        <div class="flex items-center rounded-md shadow-sm md:items-stretch">
          <button
            type="button"
            class="flex items-center justify-center rounded-l-md border border-r-0 border-gray-300 py-2 pl-3 pr-4 text-gray-400 focus:relative md:w-9 md:px-2 bg-white hover:text-gray-500 md:hover:bg-gray-50"
            @click="previous"
          >
            <span class="sr-only">Poprzedni {{ calendarState.viewMode.details.shortcut }}</span>
            <ChevronLeftIcon
              class="size-5"
              aria-hidden="true"
            />
          </button>
          <button
            type="button"
            class="hidden border-y border-gray-300 bg-white px-3.5 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900 focus:relative md:block"
            @click="today"
          >
            Dzisiaj
          </button>
          <span
            class="relative -mx-px h-5 w-px bg-gray-300 md:hidden z-10"
          />
          <button
            type="button"
            class="flex items-center justify-center rounded-r-md border border-l-0 border-gray-300 py-2 pl-4 pr-3 text-gray-400 focus:relative md:w-9 md:px-2 bg-white hover:text-gray-500 md:hover:bg-gray-50"
            @click="next"
          >
            <span class="sr-only">Następny {{ calendarState.viewMode.details.shortcut }}</span>
            <ChevronRightIcon
              class="size-5"
              aria-hidden="true"
            />
          </button>
        </div>
      </div>
      <Menu
        as="div"
        class="flex justify-end relative"
      >
        <MenuButton
          type="button"
          class="flex items-center rounded-md border border-gray-300 bg-white py-2 pl-3 pr-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50"
        >
          <span class="md:hidden">{{ calendarState.viewMode.details.shortcut }}</span>
          <span class="hidden md:inline-block">{{ calendarState.viewMode.details.name }}</span>
          <ChevronDownIcon
            class="ml-2 size-5 text-gray-400"
            aria-hidden="true"
          />
        </MenuButton>

        <transition
          class="z-10"
          enter-active-class="transition ease-out duration-100"
          enter-from-class="transform opacity-0 scale-95"
          enter-to-class="transform opacity-100 scale-100"
          leave-active-class="transition ease-in duration-75"
          leave-from-class="transform opacity-100 scale-100"
          leave-to-class="transform opacity-0 scale-95"
        >
          <MenuItems
            class="absolute right-0 mt-3 w-36 origin-top-right overflow-hidden rounded-md bg-white shadow-lg ring-1 ring-black/5 focus:outline-none"
          >
            <div class="py-1">
              <MenuItem
                v-for="option in viewModes"
                :key="option.key"
                v-slot="{ active }"
              >
                <button
                  :class="[active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'flex px-4 py-2 text-sm text-left w-full']"
                  @click="updateViewMode(option.key)"
                >
                  {{ option.shortcut }}
                  <CheckIcon
                    v-if="option.is(calendar.viewMode)"
                    class="ml-2 size-5 text-blumilk-500"
                  />
                </button>
              </MenuItem>
            </div>
          </MenuItems>
        </transition>
      </Menu>
    </div>
    <div class="border border-gray-300 lg:flex lg:flex-auto lg:flex-col">
      <div
        v-if="calendarState.viewMode.isMonth"
        class="grid grid-cols-7 gap-px border-b border-gray-300 bg-gray-300 text-center text-xs font-semibold leading-6 text-gray-700 lg:flex-none"
      >
        <div class="py-2 bg-white">
          Pon
        </div>
        <div class="py-2 bg-white">
          Wt
        </div>
        <div class="py-2 bg-white">
          Śr
        </div>
        <div class="py-2 bg-white">
          Czw
        </div>
        <div class="py-2 bg-white">
          Pt
        </div>
        <div class="py-2 bg-red-100 text-red-800">
          Sob
        </div>
        <div class="py-2 bg-red-100 text-red-800">
          Ndz
        </div>
      </div>
      <div class="flex bg-gray-300 text-xs leading-6 text-gray-700 lg:flex-auto">
        <div
          class="w-full grid grid-cols-7 gap-px"
          :class="{ 'grid-rows-1': calendarState.viewMode.isWeek }"
        >
          <DayComponent
            v-for="(day, index) in calendar.days"
            :key="index"
            :day="day"
            class="flex flex-col relative p-3"
            :class="{ 'day': calendarState.viewMode.isWeek }"
          />
        </div>
      </div>
    </div>
  </section>
</template>

<style lang="css">
.day:nth-of-type(7n - 6):before {
  content: "Pon";
}

.day:nth-of-type(7n - 5):before {
  content: "Wt";
}

.day:nth-of-type(7n - 4):before {
  content: "Śr";
}

.day:nth-of-type(7n - 3):before {
  content: "Czw";
}

.day:nth-of-type(7n - 2):before {
  content: "Pt";
}

.day:nth-of-type(7n - 1):before {
  content: "Sob";
}

.day:nth-of-type(7n):before {
  content: "Ndz";
}
</style>
