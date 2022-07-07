<template>
  <section>
    <div
      v-if="stats.hasLimit"
      class="grid grid-cols-2 gap-2 h-full"
    >
      <div class="py-5 px-4 bg-white shadow-md sm:p-6">
        <dd class="mt-1 text-4xl font-semibold text-blumilk-500">
          {{ stats.remaining }}
        </dd>
        <dt class="font-medium text-gray-700 truncate text-md">
          Pozostało
        </dt>
      </div>
      <div class="py-5 px-4 bg-white shadow-md sm:p-6">
        <dd class="mt-1 text-4xl font-semibold text-blumilk-700">
          {{ stats.used }}
        </dd>
        <dt class="font-medium text-gray-700 truncate text-md">
          Wykorzystane
        </dt>
      </div>
      <div class="py-5 px-4 bg-white shadow-md sm:p-6">
        <dt class="mt-1 text-4xl font-semibold text-blumilk-200">
          {{ stats.pending }}
        </dt>
        <dd class="font-medium text-gray-700 truncate text-md">
          Rozpatrywane
        </dd>
      </div>
      <div class="py-5 px-4 bg-white shadow-md sm:p-6">
        <dt class="mt-1 text-4xl font-semibold text-blumilk-800">
          {{ stats.limit }}
        </dt>
        <dd class="font-medium text-gray-700 truncate text-md">
          Limit urlopu
        </dd>
      </div>
      <div class="py-5 px-4 bg-white shadow-md sm:p-6">
        <dt class="mt-1 text-4xl font-semibold text-gray-500">
          {{ stats.other }}
        </dt>
        <dd class="font-medium text-gray-700 truncate text-md">
          Inne urlopy
        </dd>
      </div>
      <div class="py-5 px-4 bg-white shadow-md sm:p-6">
        <dt class="mt-1 text-4xl font-semibold text-lime-500">
          {{ stats.homeOffice }}
        </dt>
        <dd class="font-medium text-gray-700 truncate text-md">
          Praca zdalna
        </dd>
      </div>
    </div>
    <div
      v-else
      class="h-full grid grid-cols-2 gap-2 h-full"
    >
      <div class="py-5 px-4 bg-white shadow-md sm:p-6">
        <dt class="mt-1 text-4xl font-semibold text-gray-500">
          {{ stats.other }}
        </dt>
        <dd class="font-medium text-gray-700 truncate text-md">
          Inne urlopy
        </dd>
      </div>
      <div class="py-5 px-4 bg-white shadow-md sm:p-6">
        <dt class="mt-1 text-4xl font-semibold text-lime-500">
          {{ stats.homeOffice }}
        </dt>
        <dd class="font-medium text-gray-700 truncate text-md">
          Praca zdalna
        </dd>
      </div>
    </div>
  </section>
</template>

<script setup>
defineProps({
  stats: Object,
})
</script>
