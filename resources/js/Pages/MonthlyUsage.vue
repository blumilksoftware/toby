<template>
  <InertiaHead title="Wykorzystanie miesięczne urlopu" />
  <div class="bg-white shadow-md">
    <div class="flex justify-between items-center p-4 sm:px-6">
      <div class="flex items-center">
        <h2 class="text-lg leading-6 font-medium text-gray-900">
          Wykorzystanie miesięczne urlopu
        </h2>
        <div class="ml-5">
          ROK TUTAJ
        </div>
        <div class="ml-5">
          Select z typami urlopów
        </div>
      </div>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-center text-sm border border-gray-300">
        <thead>
          <tr>
            <th class="w-64 py-2 border text-lg font-semibold text-gray-800 border-gray-300" />
            <th
              class="border border-gray-300 text-sm font-semibold text-gray-900 py-4 px-2"
            >
              Limit zaległy (2021)
            </th>
            <th
              class="border border-gray-300 text-sm font-semibold text-gray-900 py-4 px-2"
            >
              Limit obecny (2022)
            </th>
            <th
              v-for="month in months"
              :key="month"
              class="border border-gray-300 text-sm font-semibold text-gray-900 py-4 px-2"
              style="min-width: 46px;"
            >
              <div>
                {{ month.shortcut }}
              </div>
            </th>
            <th
              class="border border-gray-300 text-sm font-semibold text-gray-900 py-4 px-2"
            >
              Wykorzystany
            </th>
            <th
              class="border border-gray-300 text-sm font-semibold text-gray-900 py-4 px-2"
            >
              Rozpatrywany
            </th>
            <th
              class="border border-gray-300 text-sm font-semibold text-gray-900 py-4 px-2"
            >
              Pozostało
            </th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="user in users.data"
            :key="user.id"
          >
            <th
              class="border border-gray-300 py-2 px-4"
            >
              <div class="flex justify-start items-center">
                <span class="inline-flex items-center justify-center h-10 w-10 rounded-full">
                  <img
                    class="h-10 w-10 rounded-full"
                    :src="user.avatar"
                  >
                </span>
                <div class="ml-3">
                  <div
                    class="text-sm font-medium text-gray-900 whitespace-nowrap"
                  >
                    {{ user.name }}
                  </div>
                </div>
              </div>
            </th>
            <td
              v-for="index in 17"
              :key="index"
              class="border border-gray-300"
            />
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
import {useMonthInfo} from '@/Composables/monthInfo'

export default {
  name: 'MonthlyUsage',
  components: {
  },
  props: {
    users: {
      type: Object,
      default: () => null,
    },
    can: {
      type: Object,
      default: () => null,
    },
  },
  setup() {
    const {getMonths} = useMonthInfo()
    const months = getMonths()
    return {
      months,
    }
  },
}
</script>
