<template>
  <section class="bg-white shadow-md">
    <div class="flex items-center justify-between p-4 sm:px-6">
      <h2 class="text-lg font-medium leading-6 text-gray-900">
        Kalendarz
      </h2>
      <Menu
        as="div"
        class="relative"
      >
        <MenuButton
          type="button"
          class="flex items-center rounded-md border border-gray-300 bg-white py-2 pl-3 pr-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50"
        >
          {{ viewMode }} view
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
              <MenuItem v-slot="{ active }">
                <a
                  :class="[active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'block px-4 py-2 text-sm']"
                  @click="updateViewMode('week')"
                >Week view</a>
              </MenuItem>
              <MenuItem v-slot="{ active }">
                <a
                  :class="[active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'block px-4 py-2 text-sm']"
                  @click="updateViewMode('month')"
                >Month view</a>
              </MenuItem>
            </div>
          </MenuItems>
        </transition>
      </Menu>
    </div>
    <div class="ring-1 ring-black ring-opacity-5 lg:flex lg:flex-auto lg:flex-col">
      <div class="flex bg-gray-200 text-xs leading-6 text-gray-700 lg:flex-auto">
        <div :class="[viewMode === 'week' ? 'grid-rows-1' : 'grid-rows-5', 'w-full grid grid-cols-7 gap-px']">
          <div
            v-for="day in days"
            :key="day.date"
            class="flex flex-col"
            :class="[day.isCurrentMonth ? 'bg-white' : 'bg-gray-50 text-gray-500', 'day relative py-2 px-3']"
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
import { ChevronDownIcon } from '@heroicons/vue/solid'
import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue'
import { ref, watch } from 'vue'
import { DateTime } from 'luxon'

let days = ref([])
const viewMode = ref('week')
watch(viewMode, () => {
  loadMonth()
})
loadMonth()

function loadMonth() {
  const tmpDays = []
  const currentMonth = DateTime.fromObject({ year: 2022, month: 6 }).startOf('month')

  const start = currentMonth.startOf('week')
  const end = viewMode.value === 'week' ? currentMonth.endOf('week') : currentMonth.endOf('month').endOf('week')

  for (let day = start; day < end; day = day.plus({ day: 1 })) {
    const isCurrentMonth = isInCurrentMonth(day, currentMonth)
    const okIsToday = isToday(day)

    tmpDays.push({
      date: day.toISODate(),
      isCurrentMonth,
      isToday: okIsToday,
    })
  }

  days.value = tmpDays
}

function updateViewMode(type) {
  viewMode.value = type
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

.day:nth-of-type(7n - 1):before {
  content: "Sb";
}

.day:nth-of-type(7n):before {
  content: "Nd";
}
</style>
