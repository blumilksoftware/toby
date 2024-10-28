<script setup>
import Welcome from '@/Shared/Widgets/Welcome.vue'
import VacationStats from '@/Shared/Widgets/VacationStats.vue'
import AbsenceList from '@/Shared/Widgets/AbsenceList.vue'
import RemoteWorkList from '@/Shared/Widgets/RemoteWorkList.vue'
import BirthdaysList from '@/Shared/Widgets/BirthdaysList.vue'
import UpcomingHolidays from '@/Shared/Widgets/UpcomingHolidays.vue'
import UserVacationRequests from '@/Shared/Widgets/UserVacationRequests.vue'
import PendingVacationRequests from '@/Shared/Widgets/PendingVacationRequests.vue'
import VacationCalendar from '@/Shared/Widgets/VacationCalendar.vue'
import BenefitList from '@/Shared/Widgets/BenefitList.vue'
import { useGlobalProps } from '@/Composables/useGlobalProps'
import AppLayout from '@/Shared/Layout/AppLayout.vue'

defineProps({
  current: Object,
  upcoming: Object,
  vacationRequests: Object,
  stats: Object,
  calendar: Object,
  benefits: Object,
})

const { auth } = useGlobalProps()
</script>

<template>
  <AppLayout title="Strona główna">
    <div class="grid grid-cols-1 gap-4 items-start xl:grid-cols-3 xl:gap-8">
      <div class="grid grid-cols-1 gap-4 xl:col-span-2">
        <Welcome :user="auth.user" />
        <VacationStats :stats="stats" />
        <VacationCalendar
          :approved-vacations="calendar.approvedVacations"
          :holidays="calendar.holidays"
          :pending-vacations="calendar.pendingVacations"
        />
        <PendingVacationRequests
          v-if="auth.can.listAllRequests"
          :requests="vacationRequests.data"
        />
        <UserVacationRequests
          v-else
          :label="'Moje wnioski'"
          :requests="vacationRequests.data"
        />
      </div>
      <div class="grid grid-cols-1 gap-4">
        <AbsenceList
          :absences="current.absences.data"
          :upcoming-absences="upcoming.absences.data"
        />
        <RemoteWorkList
          :remote-days="current.remoteDays.data"
          :upcoming-remote-days="upcoming.remoteDays.data"
        />
        <BirthdaysList
          :birthdays="upcoming.birthdays.data"
        />
        <UpcomingHolidays
          :holidays="upcoming.holidays.data"
        />
        <BenefitList
          :benefits="benefits.data"
          :label="'Moje benefity'"
        />
      </div>
    </div>
  </AppLayout>
</template>
