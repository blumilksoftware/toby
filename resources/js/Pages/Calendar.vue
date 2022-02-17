<template>
  <InertiaHead title="Kalendarz urlopów" />
  <div class="bg-white shadow-md">
    <div class="flex justify-between items-center p-4 sm:px-6">
      <div>
        <h2 class="text-lg leading-6 font-medium text-gray-900">
          Kalendarz urlopów
        </h2>
      </div>
      <div>
        <a
          :href="`timesheet?month=${currentMonth}`"
          class="inline-flex items-center px-4 py-3 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-blumilk-600 hover:bg-blumilk-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blumilk-500"
        >
          Pobierz plik excel
        </a>
      </div>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-center table-fixed text-sm border border-gray-300">
        <thead>
          <tr>
            <th class="w-64 py-2 border border-gray-300">
              <Menu
                as="div"
                class="relative inline-block text-left"
              >
                <div>
                  <MenuButton
                    class="inline-flex justify-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blumilk-500"
                  >
                    {{ selectedMonth.name }}
                    <ChevronDownIcon class="-mr-1 ml-2 h-5 w-5" />
                  </MenuButton>
                </div>

                <transition
                  enter-active-class="transition ease-out duration-100"
                  enter-from-class="transform opacity-0 scale-95"
                  enter-to-class="transform opacity-100 scale-100"
                  leave-active-class="transition ease-in duration-75"
                  leave-from-class="transform opacity-100 scale-100"
                  leave-to-class="transform opacity-0 scale-95"
                >
                  <MenuItems
                    class="origin-top-right absolute right-0 mt-2 w-32 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none"
                  >
                    <div class="py-1">
                      <MenuItem
                        v-for="(month, index) in months"
                        :key="index"
                        v-slot="{ active }"
                      >
                        <InertiaLink
                          href="/vacation-calendar"
                          :data="{ month: month.value }"
                          :class="[active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'flex w-full font-normal px-4 py-2 text-sm']"
                        >
                          {{ month.name }}
                          <CheckIcon
                            v-if="currentMonth === month.value"
                            class="h-5 w-5 text-blumilk-500 ml-2"
                          />
                        </InertiaLink>
                      </MenuItem>
                    </div>
                  </MenuItems>
                </transition>
              </Menu>
            </th>
            <th
              v-for="day in calendar"
              :key="day.dayOfMonth"
              class="border border-gray-300 text-lg font-semibold text-gray-900 py-4 px-2"
              :class="{ 'text-blumilk-600 bg-blumilk-25 font-black': day.isToday }"
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
            <th class="border border-gray-300 py-2 px-4">
              <div class="flex justify-start items-center">
                <span class="inline-flex items-center justify-center h-10 w-10 rounded-full">
                  <img
                    class="h-10 w-10 rounded-full"
                    :src="user.avatar"
                  >
                </span>
                <div class="ml-3">
                  <div class="text-sm font-medium text-gray-900">
                    {{ user.name }}
                  </div>
                </div>
              </div>
            </th>
            <td
              v-for="day in calendar"
              :key="day.dayOfMonth"
              class="border border-gray-300"
              :class="{'bg-red-100': day.isWeekend || day.isHoliday, 'bg-blumilk-500': day.vacations.includes(user.id) }"
            >
              <div
                v-if="day.vacations.includes(user.id)"
                class="flex justify-center items-center"
              >
                <svg
                  class="w-6 h-6 text-white"
                  xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 640 512"
                >
                  <path
                    fill="currentColor"
                    d="M115.38 136.9l102.11 37.18c35.19-81.54 86.21-144.29 139-173.7-95.88-4.89-188.78 36.96-248.53 111.8-6.69 8.4-2.66 21.05 7.42 24.72zm132.25 48.16l238.48 86.83c35.76-121.38 18.7-231.66-42.63-253.98-7.4-2.7-15.13-4-23.09-4-58.02.01-128.27 69.17-172.76 171.15zM521.48 60.5c6.22 16.3 10.83 34.6 13.2 55.19 5.74 49.89-1.42 108.23-18.95 166.98l102.62 37.36c10.09 3.67 21.31-3.43 21.57-14.17 2.32-95.69-41.91-187.44-118.44-245.36zM560 447.98H321.06L386 269.5l-60.14-21.9-72.9 200.37H16c-8.84 0-16 7.16-16 16.01v32.01C0 504.83 7.16 512 16 512h544c8.84 0 16-7.17 16-16.01v-32.01c0-8.84-7.16-16-16-16z"
                  />
                </svg>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
import {Menu, MenuButton, MenuItem, MenuItems} from '@headlessui/vue'
import {CheckIcon, ChevronDownIcon} from '@heroicons/vue/solid'
import {computed} from 'vue'
import {useMonthInfo} from '@/Composables/monthInfo'

export default {
  name: 'VacationCalendar',
  components: {
    Menu,
    MenuButton,
    MenuItem,
    MenuItems,
    CheckIcon,
    ChevronDownIcon,
  },
  props: {
    users: {
      type: Object,
      default: () => null,
    },
    calendar: {
      type: Object,
      default: () => null,
    },
    currentMonth: {
      type: String,
      default: () => 'january',
    },
  },
  setup(props) {
    const {getMonths, findMonth} = useMonthInfo()
    const months = getMonths()

    const selectedMonth = computed(() => findMonth(props.currentMonth))

    return {
      months,
      selectedMonth,
    }
  },
}
</script>
