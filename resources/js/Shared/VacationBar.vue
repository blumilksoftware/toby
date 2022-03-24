<template>
  <Popper
    hover
    class="h-full w-full"
  >
    <div class="flex bg-white text-white">
      <div
        v-show="stats.used > 0"
        :style="`background-color: ${colors.used}; flex-basis: ${calculatePercent(stats.used)}%;`"
        class="flex items-center justify-center py-2 px-0.5"
      >
        <strong>{{ stats.used }}</strong>
      </div>
      <div
        v-show="stats.pending > 0"
        :style="`background-color: ${colors.pending}; flex-basis: ${calculatePercent(stats.pending)}%;`"
        class="flex items-center justify-center py-2 px-0.5"
      >
        <strong>{{ stats.pending }}</strong>
      </div>
      <div
        v-show="stats.remaining > 0"
        :style="`background-color: ${colors.remaining}; flex-basis: ${calculatePercent(stats.remaining)}%;`"
        class="flex items-center justify-center py-2 px-0.5"
      >
        <strong>{{ stats.remaining }}</strong>
      </div>
    </div>
    <template #content>
      <div class="px-4 py-2 bg-white text-md text-gray-900 rounded-md shadow-md flext">
        <div class="flex items-center font-normal">
          <i
            class="inline-block w-5 h-3 mr-3"
            :style="`background-color: ${colors.used}`"
          />
          Wykorzystane:
          <span class="font-semibold ml-1">{{ stats.used }}</span>
        </div>
        <div class="flex items-center font-normal">
          <i
            class="inline-block w-5 h-3 mr-3"
            :style="`background-color: ${colors.pending}`"
          />
          Rozpatrywane:
          <span class="font-semibold ml-1">{{ stats.pending }}</span>
        </div>
        <div class="flex items-center font-normal">
          <i
            class="inline-block w-5 h-3 mr-3"
            :style="`background-color: ${colors.remaining}`"
          />
          Pozostałe:
          <span class="font-semibold ml-1">{{ stats.remaining }}</span>
        </div>
      </div>
    </template>
  </Popper>
</template>

<script setup>
import Popper from 'vue3-popper'

const props = defineProps({
  stats: {
    type: Object,
    default: () => ({
      used: 0,
      pending: 0,
      remaining: 0,
    }),
  },
})

const colors = {
  used: '#2C466F',
  pending: '#AABDDD',
  remaining: '#527ABA',
}

function calculatePercent(value) {
  return value / (props.stats.used + props.stats.pending + props.stats.remaining) * 100
}

</script>
