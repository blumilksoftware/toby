<script setup>
import Popper from 'vue3-popper'
import { ref } from 'vue'
import VacationPopup from '@/Shared/VacationPopup.vue'
import VacationIcon from '@/Shared/VacationTypeCalendarIcon.vue'
import InertiaLink from '@/Shared/InertiaLink.vue'

defineProps({
  day: {
    type: Object,
    required: true,
  },
})

const isActive = ref(false)

function onMouseover() {
  if (!isActive.value)
    isActive.value = true
}

function onMouseleave() {
  if (isActive.value)
    isActive.value = false
}
</script>

<template>
  <div
    class="box-border"
    :class="[
      day.isCurrentMonth ? {
        'bg-red-100': day.isWeekend,
        'bg-white hover:bg-blumilk-25': !day.isWeekend && !day.isHoliday,
        'bg-red-100 text-red-800': day.isHoliday || day.isWeekend
      } : [
        'bg-gray-50 text-gray-500',
        {
          'bg-red-50': day.isWeekend
        }
      ],
      day.isHoliday && 'font-bold cursor-default',
      (day.isPendingVacation) && `pb-0 border-b-[3px] border-dashed ${day.getVacationType.border}`,
      (day.isVacation) && `pb-0 border-b-[3px] ${day.getVacationType.border}`
    ]"
  >
    <Popper
      v-if="day.isHoliday"
      as="div"
      open-delay="200"
      hover
      offset-distance="0"
      @mouseover.passive="onMouseover"
      @mouseleave="onMouseleave"
    >
      <time
        :datetime="day.date"
        :class="[ day.isToday && 'flex size-6 items-center justify-center rounded-full bg-blumilk-500 font-semibold text-white' ]"
      >
        {{ day.dayNumber }}
      </time>
      <template #content>
        <div class="py-2 px-6 text-sm font-semibold text-left text-gray-700 bg-white rounded-lg border border-gray-400">
          {{ day.getHolidayInfo }}
        </div>
      </template>
    </Popper>
    <Popper
      v-else-if="day.isVacation || day.isPendingVacation"
      as="div"
      open-delay="200"
      hover
      offset-distance="0"
      @mouseover.passive="onMouseover"
      @mouseleave="onMouseleave"
    >
      <div class="flex justify-between">
        <time
          :datetime="day.date"
          :class="[ day.isToday && 'flex size-6 items-center justify-center rounded-full bg-blumilk-500 font-semibold text-white' ]"
        >
          {{ day.dayNumber }}
        </time>
        <div class="hidden md:inline-block">
          <VacationIcon
            :type="day.getVacationType.value"
          />
        </div>
      </div>
      <template #content>
        <VacationPopup
          :vacation="day.getVacationInfo"
          :see-vacation-details="true"
        />
      </template>
    </Popper>
    <div
      v-else-if="day.isWeekend"
      class="text-red-800"
    >
      <time
        :datetime="day.date"
        class="cursor-default"
        :class="{ 'flex size-6 items-center justify-center rounded-full bg-blumilk-500 font-semibold text-white': day.isToday }"
      >
        {{ day.dayNumber }}
      </time>
    </div>
    <InertiaLink
      v-else
      href="/vacation/requests/create"
      :data="{ 'from_date': day.date }"
      @mouseover.passive="onMouseover"
      @mouseleave="onMouseleave"
    >
      <time
        :datetime="day.date"
        :class="{ 'flex size-6 items-center justify-center rounded-full bg-blumilk-500 font-semibold text-white': day.isToday }"
      >
        {{ day.dayNumber }}
      </time>
    </InertiaLink>
  </div>
</template>
