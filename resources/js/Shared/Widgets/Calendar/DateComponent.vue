<template>
  <template>
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
        :class="[ day.isToday && 'flex h-6 w-6 items-center justify-center rounded-full bg-blumilk-500 font-semibold text-white', 'text-red-600' ]"
      >
        {{ day.dayNumber }}
      </time>
      <template #content>
        <div class="py-2 px-6 text-sm font-semibold text-left text-gray-700 bg-white rounded-lg border border-gray-400">
          {{ getHolidayDescription(day) }}
        </div>
      </template>
    </Popper>
    <div
      v-else
      :class="{ 'hello': isActive && !day.isWeekend }"
      @mouseover.passive="onMouseover"
      @mouseleave="onMouseleave"
    >
      <time
        :datetime="day.date"
        :class="{ 'flex h-6 w-6 items-center justify-center rounded-full bg-blumilk-500 font-semibold text-white': day.isToday }"
      >
        {{ day.dayNumber }}
      </time>
    </div>
  </template>
</template>

<script setup>
import Popper from 'vue3-popper'
import { defineProps, ref } from 'vue'

const props = defineProps({
  day: {
    type: Object,
    required: true,
  },
  holidaydescription: {
    type: Function,
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

function getHolidayDescription(day) {
  return props.holidaydescription(day)
}

</script>