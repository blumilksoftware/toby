<script setup>
defineProps({
  holidays: Object,
})
</script>

<template>
  <section class="bg-white shadow-md">
    <div class="p-4 sm:px-6">
      <h2 class="text-lg font-medium leading-6 text-gray-900">
        Najbliższe dni wolne
      </h2>
    </div>
    <div class="px-4 pb-5 border-t border-gray-200 sm:px-6">
      <ul class="divide-y divide-gray-200">
        <li
          v-for="holiday in holidays"
          :key="holiday.id.id"
          class="flex py-4"
        >
          <div>
            <p class="text-sm font-medium text-gray-900 capitalize">
              {{ holiday.name }}
            </p>
            <p class="text-sm text-gray-500">
              {{ holiday.displayDate }} ({{ holiday.dayOfWeek }})
              <span v-if="holiday.daysToHoliday !== 0 ">
                - {{ holiday.displayDaysToHoliday }}
              </span>
            </p>
          </div>
        </li>
        <li v-if="! holidays.length">
          <p class="py-2">
            Brak danych
          </p>
        </li>
      </ul>
      <div>
        <InertiaLink
          href="/holidays"
          class="flex justify-center items-center py-2 px-4 w-full text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blumilk-500 shadow-sm"
        >
          Zobacz wszystkie
        </InertiaLink>
      </div>
    </div>
  </section>
</template>
