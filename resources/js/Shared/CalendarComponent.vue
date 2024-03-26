<script setup>
import VacationPopup from "@/Shared/VacationPopup.vue";
import useVacationTypeInfo from "@/Composables/vacationTypeInfo";
import useCurrentYearPeriodInfo from "@/Composables/yearPeriodInfo";
import { DateTime } from 'luxon'
import Popper from 'vue3-popper'

const props = defineProps({
  holidays: Object,
  vacations: Object,
  pendingVacations: Object,
  startMonth: Number,
  endMonth: Number,
})


const { findType } = useVacationTypeInfo()
const { year } = useCurrentYearPeriodInfo()

const months = []

for (let i = props.startMonth; i < props.endMonth + 1; i++) {
  const currentMonth = DateTime.fromObject({ year: year.value, month: i }).startOf('month')

  const start = currentMonth.startOf('week')
  const end = currentMonth.endOf('month').endOf('week')

  const month = {
    name: currentMonth.monthLong,
    days: [],
  }

  for (let day = start; day < end; day = day.plus({ day: 1 })) {
    const isCurrentMonth = isInCurrentMonth(day, currentMonth)

    month.days.push({
      date: day,
      isCurrentMonth: isCurrentMonth,
      isToday: isCurrentMonth && isToday(day),
      isWeekend: isWeekend(day),
      isVacation: isCurrentMonth && isVacation(day),
      isPendingVacation: isCurrentMonth && isPendingVacation(day),
      isHoliday: isHoliday(day),
    })
  }

  months.push(month)
}

function isHoliday(date) {
  return props.holidays[date.toISODate()] !== undefined
}

function isVacation(date) {
  return props.vacations[date.toISODate()] !== undefined
}

function isPendingVacation(date) {
  return props.pendingVacations[date.toISODate()] !== undefined
}

function isToday(date) {
  return DateTime.now().hasSame(date, 'year') && DateTime.now().hasSame(date, 'day')
}

function isInCurrentMonth(date, currentMonth) {
  return currentMonth.hasSame(date, 'month')
}

function isWeekend(date) {
  return date.weekday === 6 || date.weekday === 7
}

function getVacationBorder(day) {
  const type = findType(getVacationInfo(day).type)

  return type.border
}

function getVacationInfo(day) {
  return day.isVacation ? props.vacations[day.date.toISODate()] : props.pendingVacations[day.date.toISODate()]
}
</script>
<template>
  <section
      v-for="month in months"
      :key="month.name"
      class="text-center"
  >
    <h2 class="font-semibold text-gray-900 capitalize">
      {{ month.name }}
    </h2>
    <div class="grid grid-cols-7 mt-6 text-xs font-semibold leading-6 text-gray-500">
      <div>Pn</div>
      <div>Wt</div>
      <div>Śr</div>
      <div>Cz</div>
      <div>Pt</div>
      <div>Sb</div>
      <div>Nd</div>
    </div>
    <div class="grid grid-cols-7 mt-2 text-sm ring-1 ring-gray-200 shadow">
      <template
          v-for="(day, dayIdx) in month.days"
          :key="dayIdx"
      >
        <template v-if="day.isCurrentMonth">
          <Popper
              v-if="day.isVacation || day.isPendingVacation"
              open-delay="200"
              hover
              offset-distance="0"
          >
            <div :class="[day.isPendingVacation && 'mx-0.5']">
              <button :class="[day.isPendingVacation && `border-dashed`, `${getVacationBorder(day)} isolate bg-white w-full hover:bg-blumilk-25 border-b-4 py-1.5 font-medium focus:outline-blumilk-500 cursor-default`]">
                <time
                    :datetime="day.date.toISODate()"
                    :class="[ day.isToday && 'bg-blumilk-500 font-semibold text-white rounded-full', 'mx-auto flex h-7 w-7 p-4 items-center justify-center']"
                >
                  {{ day.date.day }}
                </time>
              </button>
            </div>
            <template #content>
              <VacationPopup
                  :vacation="getVacationInfo(day)"
                  :see-vacation-details="true"
              />
            </template>
          </Popper>
          <Popper
              v-else-if="day.isHoliday"
              open-delay="200"
              hover
              offset-distance="0"
          >
            <button class="py-1.5 w-full font-medium bg-white hover:bg-blumilk-25 border-b-4 border-transparent focus:outline-blumilk-500 cursor-default">
              <time
                  :datetime="day.date.toISODate()"
                  :class="[ day.isToday && 'bg-blumilk-500 font-semibold text-white rounded-full', 'text-red-700 font-bold mx-auto flex h-7 w-7 p-4 items-center justify-center']"
              >
                {{ day.date.day }}
              </time>
            </button>
            <template #content>
              <div class="py-2 px-6 text-sm font-semibold text-left text-gray-700 bg-white rounded-lg border border-gray-400">
                {{ holidays[day.date.toISODate()] }}
              </div>
            </template>
          </Popper>
          <button
              v-else-if="day.isWeekend"
              class="py-1.5 w-full font-medium bg-white hover:bg-blumilk-25 border-b-4 border-transparent focus:outline-blumilk-500 hover:bg-transparent cursor-default"
          >
            <time
                :datetime="day.date.toISODate()"
                class="text-red-700 font-bold mx-auto flex h-7 w-7 p-4 items-center justify-center"
            >
              {{ day.date.day }}
            </time>
          </button>
          <div
              v-else
              :data="{ 'from_date': day.date.toISODate() }"
              class="py-1.5 w-full font-medium bg-white hover:bg-blumilk-25 border-b-4 border-transparent focus:outline-blumilk-500"
          >
            <time
                :datetime="day.date.toISODate()"
                :class="[ day.isToday && 'bg-blumilk-500 font-semibold text-white rounded-full', 'mx-auto flex h-7 w-7 p-4 items-center justify-center']"
            >
              {{ day.date.day }}
            </time>
          </div>
        </template>
        <div
            v-else
            class="focus:z-10 py-1.5 w-full font-medium text-gray-400 bg-gray-50 border-b-4 border-transparent"
        >
          <div class="flex justify-center items-center p-4 mx-auto w-7 h-7">
            <time :datetime="day.date.toISODate()">
              {{ day.date.day }}
            </time>
          </div>
        </div>
      </template>
    </div>
  </section>
</template>
