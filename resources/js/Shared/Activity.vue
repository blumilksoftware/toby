<template>
  <div :class="{'relative pb-8': last}">
    <span
      v-if="last"
      class="absolute top-4 left-4 -ml-px w-0.5 h-full bg-gray-200"
    />
    <div class="flex relative space-x-3">
      <div>
        <span :class="[statusInfo.outline.background, statusInfo.outline.foreground, 'h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white']">
          <component
            :is="statusInfo.outline.icon"
            class="w-5 h-5 text-white"
          />
        </span>
      </div>
      <div class="flex flex-1 justify-between pt-1.5 space-x-4 min-w-0">
        <div class="flex flex-col items-start">
          <div class="text-sm font-medium text-gray-700">
            {{ statusInfo.text }}
          </div>
          <div class="text-sm font-medium text-right text-gray-400 whitespace-nowrap">
            {{ activity.user }}
          </div>
        </div>
        <div class="flex flex-col text-sm text-right text-gray-500 whitespace-nowrap">
          <time>{{ activity.date }}</time>
          <time>{{ activity.time }}</time>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useStatusInfo } from '@/Composables/statusInfo.js'

const props = defineProps({
  activity: Object,
  last: Boolean,
})

const { findStatus } = useStatusInfo()

const statusInfo = computed(() => findStatus(props.activity.state))
</script>
