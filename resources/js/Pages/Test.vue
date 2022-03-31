<template>
  <div class="bg-white shadow-md">
    <div class="p-4 sm:px-6">
      <div>
        <h2 class="text-lg font-medium leading-6 text-gray-900">
          Podsumowanie roczne
        </h2>
      </div>
    </div>
    <div class="grid grid-cols-1 gap-8 py-8 px-4 mx-auto max-w-3xl border-t border-gray-200 sm:grid-cols-2 sm:px-6 xl:grid-cols-3 xl:px-8 xl:max-w-none 2xl:grid-cols-4">
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
        <div class="grid isolate grid-cols-7 gap-px mt-2 text-sm bg-gray-200 ring-1 ring-gray-200 shadow">
          <Component
            :is="day.isCurrentMonth ? 'button' : 'div'"
            v-for="(day, dayIdx) in month.days"
            :key="dayIdx"
            :class="[day.isCurrentMonth ? 'bg-white hover:brightness-90' : 'bg-gray-50 text-gray-400', day.isCurrentMonth && day.isToday && 'bg-blumilk-700', 'py-1.5 focus:z-10 font-medium']"
          >
            <div :class="[day.isCurrentMonth && (day.isWeekend || day.isHoliday) && 'text-red-600 font-bold', 'mx-auto flex h-7 w-7 p-4 items-center justify-center']">
              <time :datetime="day.date">
                {{ day.date.split('-').pop().replace(/^0/, '') }}
              </time>
            </div>
          </Component>
        </div>
      </section>
    </div>
  </div>
</template>

<script setup>
import { DateTime } from 'luxon'

const vacations = [
  '2022-03-21',
  '2022-03-22',
  '2022-03-23',
  '2022-03-24',
  '2022-03-25',
  '2022-03-28',
  '2022-03-29',
  '2022-03-30',
  '2022-03-31',
  '2022-04-01',
]

const holidays = [
  '2022-04-17',
  '2022-04-18',
  '2022-05-01',
  '2022-05-03',
  '2022-01-01',
  '2022-01-06',
  '2022-06-16',
  '2022-08-15',
  '2022-11-01',
  '2022-11-11',
  '2022-12-25',
  '2022-12-26',
]

const months = []

for (let i = 1; i < 13; i++) {
  const currentMonth = DateTime.fromObject({ month: i }).startOf('month')
  
  const start = currentMonth.startOf('week')
  const end = currentMonth.endOf('month').endOf('week')

  const temp = {
    name: currentMonth.monthLong,
    days: [],
  }

  for (let day = start; day < end; day = day.plus({ day: 1 })) {
    temp.days.push({
      date: day.toFormat('yyyy-MM-dd'),
      isCurrentMonth: currentMonth.hasSame(day, 'month'),
      isToday: DateTime.now().hasSame(day, 'day'),
      isWeekend: day.weekday === 6 || day.weekday === 7,
      isVacation: vacations.includes(day.toFormat('yyyy-MM-dd')),
      isHoliday: holidays.includes(day.toFormat('yyyy-MM-dd')),
    })
  }

  months.push(temp)
}

</script>