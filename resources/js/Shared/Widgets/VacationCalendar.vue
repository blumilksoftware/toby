<template>
  <section class="bg-white shadow-md">
    <div class="grid grid-cols-3 grid-rows-1 items-center justify-center p-4 sm:px-6">
      <h2 class="text-lg font-medium leading-6 text-gray-900">
        {{ calendarState.monthName }} {{ calendar.currents.year !== selectedYear ? calendar.currents.year : undefined }}
      </h2>
      <div class="flex justify-center">
        <div class="flex items-center rounded-md shadow-sm md:items-stretch">
          <button
            v-if="calendarState.isPrevious"
            type="button"
            class="flex items-center justify-center rounded-l-md border border-r-0 border-gray-300 bg-white py-2 pl-3 pr-4 text-gray-400 hover:text-gray-500 focus:relative md:w-9 md:px-2 md:hover:bg-gray-50"
            @click="toLast"
          >
            <span class="sr-only">Poprzedni {{ calendarState.viewMode.details.shortcut }}</span>
            <ChevronLeftIcon
              class="h-5 w-5"
              aria-hidden="true"
            />
          </button>
          <button
            type="button"
            class="hidden border-t border-b border-gray-300 bg-white px-3.5 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900 focus:relative md:block"
            :class="{ 'rounded-l-md border border-r-0': !calendarState.isPrevious, 'rounded-r-md border border-l-0': !calendarState.isNext }"
            @click="resetCalendar"
          >
            Dziś
          </button>
          <span class="relative -mx-px h-5 w-px bg-gray-300 md:hidden" />
          <button
            v-if="calendarState.isNext"
            type="button"
            class="flex items-center justify-center rounded-r-md border border-l-0 border-gray-300 bg-white py-2 pl-4 pr-3 text-gray-400 hover:text-gray-500 focus:relative md:w-9 md:px-2 md:hover:bg-gray-50"
            @click="toNext"
          >
            <span class="sr-only">Następny {{ calendarState.viewMode.details.shortcut }}</span>
            <ChevronRightIcon
              class="h-5 w-5"
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
          {{ calendarState.viewMode.details.name }}
          <ChevronDownIcon
            class="ml-2 h-5 w-5 text-gray-400"
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
          <MenuItems class="absolute right-0 mt-3 w-36 origin-top-right overflow-hidden rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
            <div class="py-1">
              <MenuItem
                v-for="option in viewModes.all"
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
                    class="ml-2 w-5 h-5 text-blumilk-500"
                  />
                </button>
              </MenuItem>
            </div>
          </MenuItems>
        </transition>
      </Menu>
    </div>
    <div class="ring-1 ring-black ring-opacity-5 lg:flex lg:flex-auto lg:flex-col">
      <div
        v-if="calendarState.viewMode.isMonth"
        class="grid grid-cols-7 gap-px border-b border-gray-300 bg-gray-200 text-center text-xs font-semibold leading-6 text-gray-700 lg:flex-none"
      >
        <div class="bg-white py-2">
          Pn
        </div>
        <div class="bg-white py-2">
          Wt
        </div>
        <div class="bg-white py-2">
          Śr
        </div>
        <div class="bg-white py-2">
          Cz
        </div>
        <div class="bg-white py-2">
          Pt
        </div>
        <div class="bg-red-100 py-2">
          Sb
        </div>
        <div class="bg-red-100 py-2">
          Nd
        </div>
      </div>
      <div class="flex bg-gray-200 text-xs leading-6 text-gray-700 lg:flex-auto">
        <div :class="[calendarState.viewMode.isWeek ? 'grid-rows-1' : 'grid-rows-5', 'w-full grid grid-cols-7 gap-px']">
          <div
            v-for="(day, index) in days"
            :key="index"
            class="flex flex-col"
            :class="[day.isCurrentMonth ? 'bg-white' : 'bg-gray-50 text-gray-500', calendarState.viewMode.isWeek && index !== 0 && (index % 5 === 0 || index % 6 === 0) ? 'bg-red-100' : undefined, calendarState.viewMode.isWeek ? 'day' : 'month-day', 'relative py-2 px-3']"
          >
            <time
              :datetime="day.date"
              :class="day.isToday ? 'flex h-6 w-6 items-center justify-center rounded-full bg-indigo-600 font-semibold text-white' : undefined"
            >
              {{ day.date.split('-').pop().replace(/^0/, '') }}
            </time>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { CheckIcon, ChevronDownIcon, ChevronLeftIcon, ChevronRightIcon } from '@heroicons/vue/solid'
import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue'
import { ref, watch, computed, reactive } from 'vue'
import { DateTime } from 'luxon'
import useCurrentYearPeriodInfo from '@/Composables/yearPeriodInfo'
import { useMonthInfo } from '@/Composables/monthInfo'
import viewModes from '@/Shared/Widgets/VacationCalendar/ViewModeOptions'

let days = ref([])
const months = useMonthInfo().getMonths()
function getCurrentDate() {
  const { year, month, weekNumber } = DateTime.now()
  return { year, month, weekNumber }
}
const selectedYear = useCurrentYearPeriodInfo().year.value
const currentDate = getCurrentDate()

const calendar = {
  viewMode: ref('week'),
  currents: reactive({
    year: selectedYear,
    month: selectedYear === currentDate.year ? currentDate.month : 1,
    week: selectedYear === currentDate.year ? currentDate.weekNumber : 1,
  }),
  toPreviousActive: computed(() => calendar.currents.month !== 1),
}

const calendarState = reactive({
  viewMode: {
    isWeek: computed(() => calendar.viewMode.value === 'week'),
    isMonth: computed(() => calendar.viewMode.value === 'month'),
    details: computed(() => viewModes.find(calendar.viewMode.value)),
  },
  monthName: computed(() => months[calendar.currents.month - 1]?.name),
  isActualYear: computed(() => calendar.currents.year !== selectedYear),
  isPrevious: computed(() => calendar.currents.month !== 1),
  isNext: computed(() => calendar.currents.month !== 12),
})

watch([calendar.viewMode, calendar.currents], () => {
  loadCalendar()
})

loadCalendar()

function loadCalendar() {
  console.log('Loaded')
  let focusDate = DateTime.fromObject({
    year: calendar.currents.year,
    month: calendar.currents.month,
  })
  let start, end

  if (calendar.viewMode.value === 'week') {
    if (currentDate.year === selectedYear)
      focusDate = DateTime.fromObject({ weekNumber: calendar.currents.week })
    else
      focusDate = focusDate.plus({ week: calendar.currents.week - 1 })
    start = focusDate.startOf('week')
    end = focusDate.endOf('week')
  } else if (calendar.viewMode.value === 'month') {
    focusDate = focusDate.startOf('month')
    start = focusDate.startOf('week')
    end = focusDate.endOf('month').endOf('week')
  }

  generateCalendarData(start, end, focusDate)
}

function generateCalendarData(startDate, endDate, focusDate) {
  const tmpDays = []
  for (let day = startDate; day < endDate; day = day.plus({ day: 1 })) {
    const isCurrentMonth = isInCurrentMonth(day, focusDate)
    const okIsToday = isToday(day)

    tmpDays.push({
      date: day.toISODate(),
      isCurrentMonth,
      isToday: okIsToday,
    })
  }

  days.value = tmpDays
}

function toLast() {
  if (calendar.viewMode.value === 'week')
    addWeeks(-1)
  else
    addMonths(-1)
}

function toNext() {
  if (calendar.viewMode.value === 'week') {
    addWeeks()
  } else
    addMonths()
}

function resetCalendar() {
  calendar.currents.year = selectedYear
  calendar.currents.month = selectedYear === currentDate.year ? currentDate.month : 1
  calendar.currents.week = selectedYear === currentDate.year ? currentDate.weekNumber : 1
}

function addWeeks(howMany = 1) {
  calendar.currents.week += howMany
}

function addMonths(howMany = 1) {
  calendar.currents.month += howMany
}

function updateViewMode(type) {
  resetCalendar()
  calendar.viewMode.value = type
}

function isInCurrentMonth(date, currentMonth) {
  return currentMonth.hasSame(date, 'month')
}

function isToday(date) {
  return date.toISODate() === DateTime.local().toISODate()
}
</script>

<style lang="css">
.day:nth-of-type(7n - 6):before {
  content: "Pn";
}

.day:nth-of-type(7n - 5):before {
  content: "Wt";
}

.day:nth-of-type(7n - 4):before {
  content: "Śr";
}

.day:nth-of-type(7n - 3):before {
  content: "Cz";
}

.day:nth-of-type(7n - 2):before {
  content: "Pt";
}

.day:nth-of-type(7n - 1):before,
.day:nth-of-type(7n):before {
  color: #991b1b;
}

.day:nth-of-type(7n - 1):before {
  content: "Sb";
}

.day:nth-of-type(7n):before {
  content: "Nd";
}

.month-day:nth-of-type(7n - 1), .month-day:nth-of-type(7n) {
  background-color: #fee2e2;
}
</style>
