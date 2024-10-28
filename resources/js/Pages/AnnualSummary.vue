<script setup>
import CalendarComponent from '@/Shared/CalendarComponent.vue'
import YearPicker from '@/Shared/Forms/YearPicker.vue'
import { ref, watch } from 'vue'
import { DateTime } from 'luxon'
import AppLayout from '@/Shared/Layout/AppLayout.vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
  year: Number,
  holidays: Object,
  vacations: Object,
  pendingVacations: Object,
  overButtonDay: String,
})
const currentDate = DateTime.now()
const selectedYear = ref(props.year)

watch(selectedYear, (value, oldValue) => {
  if (value === oldValue)
    return

  router.visit('/vacation/annual-summary', {
    data: { year: value },
  })
})
</script>

<template>
  <AppLayout title="Podsumowanie roczne">
    <div class="bg-white shadow-md">
      <div class="p-4 sm:px-6">
        <h2 class="text-lg font-medium leading-6 text-gray-900">
          Podsumowanie roczne
          <YearPicker
            v-model="selectedYear"
            :from="currentDate.year - 20"
            :to="currentDate.year + 1"
            class="inline-block ml-2"
          />
        </h2>
      </div>
      <div
        class="grid grid-cols-1 gap-8 py-8 px-4 mx-auto max-w-3xl border-t border-gray-200 sm:grid-cols-2 sm:px-6 xl:grid-cols-3 xl:px-8 xl:max-w-none 2xl:grid-cols-4"
      >
        <CalendarComponent
          :year="year"
          :start-month="1"
          :end-month="12"
          :vacations="vacations"
          :pending-vacations="pendingVacations"
          :holidays="holidays"
        />
      </div>
    </div>
  </AppLayout>
</template>
