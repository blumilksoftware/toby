<script setup>
import VacationPopup from '@/Shared/VacationPopup.vue'
import VacationTypeCalendarIcon from '@/Shared/VacationTypeCalendarIcon.vue'
import Popper from 'vue3-popper'

const props = defineProps({
  vacation: Object,
  seeVacationDetails: Boolean,
})

</script>

<template>
  <Popper
    open-delay="200"
    hover
    offset-distance="0"
  >
    <VacationTypeCalendarIcon
      :type="props.vacation.type"
      :opacity="props.vacation.pending"
    />
    <template #content>
      <VacationPopup
        :vacation="props.vacation"
        :see-vacation-details="props.seeVacationDetails"
      />
    </template>
  </Popper>
</template>
