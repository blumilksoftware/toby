<template>
  <section class="grid grid-cols-1 gap-4 md:grid-cols-2">
    <div class="hidden p-4 bg-white shadow-md md:block">
      <VacationChart :stats="stats" />
    </div>
    <div class="h-full">
      <div class="grid grid-cols-2 gap-4 h-full">
        <div class="py-5 px-4 bg-white shadow-md sm:p-6">
          <dd class="mt-1 text-4xl font-semibold text-blumilk-500">
            {{ stats.remaining }}
          </dd>
          <dt class="font-medium text-gray-700 truncate text-md">
            Pozostało
          </dt>
          <dt class="mt-2 text-sm font-medium text-gray-500">
            Dni do wykorzystania teraz.
          </dt>
        </div>
        <div class="py-5 px-4 bg-white shadow-md sm:p-6">
          <dd class="mt-1 text-4xl font-semibold text-blumilk-700">
            {{ stats.used }}
          </dd>
          <dt class="font-medium text-gray-700 truncate text-md">
            Wykorzystane
          </dt>
          <dt class="mt-2 text-sm font-medium text-gray-500">
            Dni, które zostały już wykorzystane na urlop wypoczynkowy.
          </dt>
        </div>
        <div class="py-5 px-4 bg-white shadow-md sm:p-6">
          <dt class="mt-1 text-4xl font-semibold text-blumilk-200">
            {{ stats.pending }}
          </dt>
          <dd class="font-medium text-gray-700 truncate text-md">
            Rozpatrywane
          </dd>
          <dt class="mt-2 text-sm font-medium text-gray-500">
            Dni czekające na akceptację przełożonych.
          </dt>
        </div>
        <div class="py-5 px-4 bg-white shadow-md sm:p-6">
          <dt class="mt-1 text-4xl font-semibold text-gray-900">
            {{ stats.limit }}
          </dt>
          <dd class="font-medium text-gray-700 truncate text-md">
            Limit urlopu
          </dd>
          <dt class="mt-2 text-sm font-medium text-gray-500">
            Twój roczny limit urlopu wypoczynkowego.
          </dt>
        </div>
        <div class="col-span-2 py-5 px-4 bg-white shadow-md sm:p-6">
          <dt class="mt-1 text-4xl font-semibold text-gray-900">
            {{ stats.other }}
          </dt>
          <dd class="font-medium text-gray-700 truncate text-md">
            Inne urlopy
          </dd>
          <dt class="mt-2 text-sm font-medium text-gray-500">
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
