<script setup>
import CalendarComponent from '@/Shared/CalendarComponent.vue'

defineProps({
  holidays: Object,
  vacations: Object,
  pendingVacations: Object,
  overButtonDay: String,
})
</script>

<template>
  <InertiaHead title="Podsumowanie roczne" />
  <div class="bg-white shadow-md">
    <div class="p-4 sm:px-6">
      <h2 class="text-lg font-medium leading-6 text-gray-900">
        Podsumowanie roczne
      </h2>
    </div>
    <div
      class="grid grid-cols-1 gap-8 py-8 px-4 mx-auto max-w-3xl border-t border-gray-200 sm:grid-cols-2 sm:px-6 xl:grid-cols-3 xl:px-8 xl:max-w-none 2xl:grid-cols-4"
    >
      <CalendarComponent
        :start-month="1"
        :end-month="12"
        :vacations="vacations"
        :pending-vacations="pendingVacations"
        :holidays="holidays"
      />
    </div>
  </div>
</template>
