<template>
  <section class="bg-white shadow-md">
    <div class="p-4 sm:px-6">
      <h2 class="text-lg leading-6 font-medium text-gray-900">
        Najbliższe dni wolne
      </h2>
    </div>
    <div class="border-t border-gray-200 px-4 pb-5 sm:px-6">
      <ul class="divide-y divide-gray-200">
        <li
          v-for="holiday in holidays"
          :key="holiday.id.id"
          class="py-4 flex"
        >
          <div>
            <p class="text-sm font-medium text-gray-900">
              {{ holiday.name }}
            </p>
            <p class="text-sm text-gray-500">
              {{ holiday.displayDate }}
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
          class="w-full flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blumilk-500"
        >
          Zobacz wszystkie
        </InertiaLink>
      </div>
    </div>
  </section>
</template>

<script setup>
defineProps({
  holidays: Object,
})
</script>