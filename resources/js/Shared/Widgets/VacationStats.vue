<template>
  <section class="grid grid-cols-2 gap-4">
    <div class="bg-white shadow-md p-4">
      <VacationChart :stats="stats" />
    </div>
    <div class="h-full">
      <div class="grid grid-cols-2 gap-4 h-full">
        <div class="px-4 py-5 bg-white shadow-md sm:p-6">
          <dd class="mt-1 text-4xl font-semibold text-blumilk-500">
            {{ stats.remaining }}
          </dd>
          <dt class="text-md font-medium text-gray-700 truncate">
            Pozostało
          </dt>
          <dt class="text-sm font-medium text-gray-500 mt-2">
            Dni do wykorzystania teraz.
          </dt>
        </div>
        <div class="px-4 py-5 bg-white shadow-md sm:p-6">
          <dd class="mt-1 text-4xl font-semibold text-blumilk-700">
            {{ stats.used }}
          </dd>
          <dt class="text-md font-medium text-gray-700 truncate">
            Dni wykorzystane
          </dt>
          <dt class="text-sm font-medium text-gray-500 mt-2">
            Dni, które zostały już wykorzystane na urlop wypoczynkowy.
          </dt>
        </div>
        <div class="px-4 py-5 bg-white shadow-md sm:p-6">
          <dt class="mt-1 text-4xl font-semibold text-blumilk-200">
            {{ stats.pending }}
          </dt>
          <dd class="text-md font-medium text-gray-500 truncate">
            Rozpatrywane
          </dd>
          <dt class="text-sm font-medium text-gray-500 mt-2">
            Dni czekające na akceptację przełożonych.
          </dt>
        </div>
        <div class="px-4 py-5 bg-white shadow-md sm:p-6">
          <dt class="mt-1 text-4xl font-semibold text-gray-900">
            {{ stats.limit }}
          </dt>
          <dd class="text-md font-medium text-gray-500 truncate">
            Limit urlopu
          </dd>
          <dt class="text-sm font-medium text-gray-500 mt-2">
            Twój roczny limit urlopu wypoczynkowego.
          </dt>
        </div>
        <div class="px-4 py-5 bg-white shadow-md sm:p-6 col-span-2">
          <dt class="mt-1 text-4xl font-semibold text-gray-900">
            {{ stats.other }}
          </dt>
          <dd class="text-md font-medium text-gray-500 truncate">
            Inne urlopy
          </dd>
          <dt class="text-sm font-medium text-gray-500 mt-2">
            Urlopy bezpłatne, okolicznościowe, zwolnienia lekarskie, itd., które zostały już zatwierdzone.
          </dt>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import VacationChart from '@/Shared/VacationChart'

defineProps({
  stats: Object,
})
</script>