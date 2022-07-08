<template>
  <section class="bg-white shadow-md">
    <div class="grid grid-cols-3 grid-rows-1 items-center justify-center p-4 sm:px-6">
      <h2 class="text-lg font-medium leading-6 text-gray-900">
        {{ calendarState.monthName }} {{ !calendarState.isActualYear ? calendar.currents.year : undefined }}
      </h2>
      <div class="flex justify-center">
        <div class="flex items-center rounded-md shadow-sm md:items-stretch">
          <button
            type="button"
            class="flex items-center justify-center rounded-l-md border border-r-0 border-gray-300 py-2 pl-3 pr-4 text-gray-400 focus:relative md:w-9 md:px-2"
            :class="[ calendarState.isPrevious ? 'bg-white hover:text-gray-500 md:hover:bg-gray-50' : 'bg-gray-100' ]"
            @click="toPrevious"
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
            @click="goToToday"
          >
            Dzisiaj
          </button>
          <span
            class="relative -mx-px h-5 w-px bg-gray-300 md:hidden z-10"
          />
          <button
            type="button"
            class="flex items-center justify-center rounded-r-md border border-l-0 border-gray-300 py-2 pl-4 pr-3 text-gray-400 focus:relative md:w-9 md:px-2"
            :class="[ calendarState.isNext ? 'bg-white hover:text-gray-500 md:hover:bg-gray-50' : 'bg-gray-100' ]"
            :disabled="!calendarState.isNext"
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
          <span class="md:hidden">{{ calendarState.viewMode.details.shortcut }}</span>
          <span class="hidden md:inline-block">{{ calendarState.viewMode.details.name }}</span>
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
                    class="ml-2 w-5 h-5 text-blumilk-500"
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
            v-for="(day, index) in days"
            :key="index"
            :day="day"
            class="flex flex-col relative py-3 px-3"
            :class="{ 'day': calendarState.viewMode.isWeek }"
          />
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
import useVacationTypeInfo from '@/Composables/vacationTypeInfo'
import useCurrentYearPeriodInfo from '@/Composables/yearPeriodInfo'
import { useMonthInfo } from '@/Composables/monthInfo'
import { viewModes, find as findViewMode } from '@/Shared/Widgets/Calendar/ViewModeOptions'
import DayComponent from '@/Shared/Widgets/Calendar/DayComponent'

const props = defineProps({
  holidays: Object,
  approvedVacations: Object,
  pendingVacations: Object,
})

let days = ref([])

function getCurrentDate() {
  const { year, month, weekNumber } = DateTime.now()
  return { year, month, week: weekNumber }
}
const currentDate = getCurrentDate()

const months = useMonthInfo().getMonths()
const { findType } = useVacationTypeInfo()
const selectedYear = useCurrentYearPeriodInfo().year.value
const weeksInYear = DateTime.fromObject({ year: selectedYear, month: 12, day: 31 }).weekNumber

const calendar = {
  viewMode: ref(localStorage.getItem('calendarViewMode') ?? 'week'),
  currents: reactive({
    year: selectedYear,
    month: selectedYear === currentDate.year ? currentDate.month : 1,
    week: selectedYear === currentDate.year ? currentDate.week : 0,
  }),
}

const calendarState = reactive({
  viewMode: {
    isWeek: computed(() => calendar.viewMode.value === 'week'),
    isMonth: computed(() => calendar.viewMode.value === 'month'),
    details: computed(() => findViewMode(calendar.viewMode.value)),
  },
  monthName: computed(() => months[calendar.currents.month - 1]?.name),
  isActualYear: computed(() => calendar.currents.year === DateTime.now().year),
  isPrevious: computed(() => calendarState.viewMode.isMonth ? calendar.currents.month !== 1 : calendar.currents.week > 0),
  isNext: computed(() => calendarState.viewMode.isMonth ? calendar.currents.month !== 12 : calendar.currents.week < weeksInYear),
})

const customCalendar = {
  loadCalendar() {
    const date = DateTime.fromObject({
      year: calendar.currents.year,
      month: calendarState.viewMode.isMonth ? calendar.currents.month : 1,
      day: 1,
    })
    days.value = this.generateCalendarData(date)
  },
  generateCalendarData(date) {
    const firstMonthDay = date.startOf('month')
    const lastMonthDay = date.endOf('month')

    if (calendarState.viewMode.isWeek) {
      return this.generateWeekData(firstMonthDay.startOf('week'))
    } else if (calendarState.viewMode.isMonth) {
      return this.generateMonthData(firstMonthDay.startOf('week'), lastMonthDay.endOf('week'))
    }
    return []
  },
  generateWeekData(start) {
    let days = [], startWeek
    if (calendar.currents.month === 1 && calendar.currents.week === 0)
      startWeek = DateTime.fromObject({ year: calendar.currents.year, month: 1, day: 1 }).startOf('week')
    else if (calendarState.isActualYear)
      startWeek = DateTime.fromObject({ weekNumber: calendar.currents.week })
    else
      startWeek = start.plus({ week: calendar.currents.week })
    const endWeek = startWeek.endOf('week')
    for (let day = startWeek; day < endWeek; day = day.plus({ day: 1 })) {
      days.push(this.prepareDay(day))
    }
    return days
  },
  generateMonthData(start, end) {
    let days = []
    for (let day = start; day < end; day = day.plus({ day: 1 })) {
      days.push(this.prepareDay(day))
    }

    return days
  },
  prepareDay(day) {
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
  },
}

watch([calendar.viewMode, calendar.currents], () => {
  customCalendar.loadCalendar()
})

customCalendar.loadCalendar()

function toPrevious() {
  if (calendarState.isPrevious) {
    if (calendar.viewMode.value === 'week')
      minusWeek()
    else
      minusMonth()
  }
}

function toNext() {
  if (calendarState.isNext) {
    if (calendar.viewMode.value === 'week')
      addWeek()
    else
      addMonth()
  }
}

function resetCalendar(config = {}) {
  const currentMonth = isUndefined(config.month) ? currentDate.month : config.month
  const currentWeek = isUndefined(config.week) ? currentDate.week : config.week

  calendar.currents.year = isUndefined(config.year) ? selectedYear : config.month
  calendar.currents.month = calendarState.isActualYear || !isUndefined(config.year) ? currentMonth : 1
  calendar.currents.week = calendarState.isActualYear || !isUndefined(config.week) ? currentWeek : 0
}

function isUndefined(value) {
  return value === undefined
}

function addWeek(minus = false) {
  const howMany = minus ? -1: 1
  let nextMonth, date = DateTime.fromObject({
    year: calendar.currents.year,
    month: 1,
    day: 1,
  })

  date = date.plus({ week: calendar.currents.week + howMany })

  const startWeekDay = date.startOf('week'), endWeekDay = date.endOf('week')
  nextMonth = howMany > 0 ? startWeekDay.month : endWeekDay.month
  if (howMany < 0 && endWeekDay.day === endWeekDay.daysInMonth)
    calendar.currents.week--
  else if (howMany > 0 && startWeekDay.day === 1)
    calendar.currents.week++

  if (nextMonth !== calendar.currents.month) {
    calendar.currents.month = calendar.currents.week > 1 ? nextMonth : 1
    if (calendar.currents.week === 1)
      calendar.currents.week = 0
    return
  }
  calendar.currents.week += howMany
}

function minusWeek() {
  addWeek(true)
}

function addMonth(minus = false) {
  calendar.currents.month += minus ? -1 : 1
}

function minusMonth() {
  addMonth(true)
}

function goToToday() {
  resetCalendar({ year: currentDate.year, months: currentDate.month, week: currentDate.week })
}

function updateViewMode(type) {
  if (type === 'month')
    resetCalendar({ week: 0 })
  else
    resetCalendar()
  calendar.viewMode.value = type
  localStorage.setItem('calendarViewMode', type)
}

function isInCurrentMonth(date) {
  return calendar.currents.month === date.month
}

function isWeekend(date) {
  return date.weekday === 6 || date.weekday === 7
}

function isToday(date) {
  return date.toISODate() === DateTime.local().toISODate()
}

function isHoliday(date) {
  return props.holidays[date.toISODate()] !== undefined
}

function getHolidayInfo(day) {
  return props.holidays[day.date]
}

function isVacation(date) {
  return props.approvedVacations[date.toISODate()] !== undefined
}

function isPendingVacation(date) {
  return props.pendingVacations[date.toISODate()] !== undefined
}

function getVacationType(day) {
  return findType(getVacationInfo(day).type)
}

function getVacationInfo(day) {
  return day.isVacation ? props.approvedVacations[day.date] : props.pendingVacations[day.date]
}
</script>

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
