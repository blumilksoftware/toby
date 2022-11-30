<script setup>
defineProps({
  stats: Object,
})
</script>

<template>
  <section>
    <div
      v-if="stats.hasLimit"
      class="grid grid-cols-2 2xl:grid-cols-4 gap-2 mb-2 2xl:mb-4"
    >
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
        <dt class="mt-1 text-4xl font-semibold text-blumilk-800">
          {{ stats.limit }}
        </dt>
        <dd class="font-medium text-gray-700 truncate text-md">
          Limit urlopu
        </dd>
        <dt class="mt-2 text-sm font-medium text-gray-500">
          Roczny limit urlopu wypoczynkowego.
        </dt>
      </div>
    </div>
    <div
      class="grid grid-cols-2 gap-2"
    >
      <div class="py-5 px-4 bg-white shadow-md sm:p-6">
        <dt class="mt-1 text-4xl font-semibold text-gray-500">
          {{ stats.other }}
        </dt>
        <dd class="font-medium text-gray-700 truncate text-md">
          Inne urlopy
        </dd>
        <dt class="mt-2 text-sm font-medium text-gray-500">
          Urlopy bezpłatne, okolicznościowe, zwolnienia lekarskie, itd., które zostały już zatwierdzone.
        </dt>
      </div>
      <div class="py-5 px-4 bg-white shadow-md sm:p-6">
        <dt class="mt-1 text-4xl font-semibold text-lime-500">
          {{ stats.remoteWork }}
        </dt>
        <dd class="font-medium text-gray-700 truncate text-md">
          Praca zdalna
        </dd>
        <dt class="mt-2 text-sm font-medium text-gray-500">
          Dni przepracowane poza biurem.
        </dt>
      </div>
    </div>
  </section>
</template>
