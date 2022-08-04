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
        :upcoming-absences="upcomingAbsences.data"
      />
      <RemoteWorkList
        :remote-days="remoteDays.data"
        :upcoming-remote-days="upcomingRemoteDays.data"
      />
      <UpcomingHolidays
        v-if="years.current.year === years.selected.year && holidays.data.length"
        :holidays="holidays.data"
      />
      <BenefitList
        :benefits="benefits.data"
      />
    </div>
  </div>
</template>

<script setup>
import Welcome from '@/Shared/Widgets/Welcome.vue'
import VacationStats from '@/Shared/Widgets/VacationStats.vue'
import AbsenceList from '@/Shared/Widgets/AbsenceList.vue'
import RemoteWorkList from '@/Shared/Widgets/RemoteWorkList.vue'
import UpcomingHolidays from '@/Shared/Widgets/UpcomingHolidays.vue'
import UserVacationRequests from '@/Shared/Widgets/UserVacationRequests.vue'
import PendingVacationRequests from '@/Shared/Widgets/PendingVacationRequests.vue'
import VacationCalendar from '@/Shared/Widgets/VacationCalendar.vue'
import BenefitList from '@/Shared/Widgets/BenefitList.vue'

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
  allHolidays: Object,
  approvedVacations: Object,
  pendingVacations: Object,
  benefits: Object,
})
</script>
