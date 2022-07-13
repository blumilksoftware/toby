<template>
  <InertiaHead title="Strona główna" />
  <div class="grid grid-cols-1 gap-4 items-start xl:grid-cols-3 xl:gap-8">
    <div class="grid grid-cols-1 gap-4 xl:col-span-2">
      <Welcome :user="auth.user" />
      <VacationCalendar
        :holidays="allHolidays"
        :approved-vacations="approvedVacations"
        :pending-vacations="pendingVacations"
      />
      <PendingVacationRequests
        v-if="can.listAllVacationRequests"
        :requests="vacationRequests.data"
      />
      <UserVacationRequests
        v-else
        :requests="vacationRequests.data"
      />
    </div>
    <div class="grid grid-cols-1 gap-4">
      <VacationStats :stats="stats" />
      <AbsenceList
        :absences="absences.data"
      />
      <RemoteWorkList
        :remote-days="remoteDays.data"
      />
      <UpcomingHolidays
        v-if="years.current.year === years.selected.year && holidays.data.length"
        :holidays="holidays.data"
      />
    </div>
  </div>
</template>

<script setup>
import Welcome from '@/Shared/Widgets/Welcome'
import VacationStats from '@/Shared/Widgets/VacationStats'
import AbsenceList from '@/Shared/Widgets/AbsenceList'
import RemoteWorkList from '@/Shared/Widgets/RemoteWorkList'
import UpcomingHolidays from '@/Shared/Widgets/UpcomingHolidays'
import UserVacationRequests from '@/Shared/Widgets/UserVacationRequests'
import PendingVacationRequests from '@/Shared/Widgets/PendingVacationRequests'
import VacationCalendar from '@/Shared/Widgets/VacationCalendar'

defineProps({
  auth: Object,
  absences: Object,
  remoteDays: Object,
  vacationRequests: Object,
  holidays: Object,
  can: Object,
  stats: Object,
  years: Object,
  allHolidays: Object,
  approvedVacations: Object,
  pendingVacations: Object,
})
</script>
