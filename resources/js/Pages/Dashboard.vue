<template>
  <InertiaHead title="Strona główna" />
  <div class="grid grid-cols-1 gap-4 items-start xl:grid-cols-3 xl:gap-8">
    <div class="grid grid-cols-1 gap-4 xl:col-span-2">
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
      <AbsenceList
        v-if="years.current.year === years.selected.year && absences.data.length"
        :absences="absences.data"
        :upcoming-absences="absences.data"
      />
      <HomeOfficeList
        v-if="years.current.year === years.selected.year && remoteDays.data.length"
        :remote-days="remoteDays.data"
        :upcoming-remote-days="upcomingRemoteDays.data"
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
import HomeOfficeList from '@/Shared/Widgets/HomeOfficeList'
import UpcomingHolidays from '@/Shared/Widgets/UpcomingHolidays'
import UserVacationRequests from '@/Shared/Widgets/UserVacationRequests'
import PendingVacationRequests from '@/Shared/Widgets/PendingVacationRequests'

defineProps({
  auth: Object,
  absences: Object,
  remoteDays: Object,
  upcomingAbsences: Object,
  upcomingRemoteDays: Object,
  vacationRequests: Object,
  holidays: Object,
  can: Object,
  stats: Object,
  years: Object,
})
</script>
