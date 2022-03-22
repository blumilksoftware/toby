<template>
  <section class="bg-white shadow-md">
    <div class="p-4 sm:px-6">
      <h2 class="text-lg leading-6 font-medium text-gray-900">
        Dzisiejsze nieobecności
      </h2>
    </div>
    <div class="border-t border-gray-200 px-4 sm:px-6">
      <ul class="divide-y divide-gray-200">
        <li
          v-for="absence in absences"
          :key="absence.user.id"
          class="py-4 flex"
        >
          <img
            class="h-10 w-10 rounded-full"
            :src="absence.user.avatar"
          >
          <div class="ml-3">
            <p class="text-sm font-medium text-gray-900">
              {{ absence.user.name }}
            </p>
            <p class="text-sm text-gray-500">
              {{ absence.user.email }}
            </p>
          </div>
        </li>
        <li v-if="! absences.length">
          <p class="py-2">
            Brak danych
          </p>
        </li>
      </ul>
    </div>
  </section>
</template>

<script setup>
defineProps({
  absences: Object,
})
</script>