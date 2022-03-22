<template>
  <InertiaHead title="Strona główna" />
  <div class="grid grid-cols-1 gap-4 items-start lg:grid-cols-3 lg:gap-8">
    <div class="grid grid-cols-1 gap-4 lg:col-span-2">
      <Welcome :user="auth.user" />
      <VacationStats :stats="stats" />
    </div>
    <div class="grid grid-cols-1 gap-4">
      <PendingVacationRequests
        v-if="can.listAllVacationRequests"
        :requests="vacationRequests.data"
      />
      <UserVacationRequests
        v-else
        :requests="vacationRequests.data"
      />
      <AbsenceList :absences="absences.data" />
      <UpcomingHolidays :holidays="holidays.data" />
    </div>
  </div>
</template>

<script setup>
import Welcome from '@/Shared/Widgets/Welcome'
import VacationStats from '@/Shared/Widgets/VacationStats'
import AbsenceList from '@/Shared/Widgets/AbsenceList'
import UpcomingHolidays from '@/Shared/Widgets/UpcomingHolidays'
import UserVacationRequests from '@/Shared/Widgets/UserVacationRequests'
import PendingVacationRequests from '@/Shared/Widgets/PendingVacationRequests'

defineProps({
  auth: Object,
  absences: Object,
  vacationRequests: Object,
  holidays: Object,
  can: Object,
  stats: Object,
})
</script>
