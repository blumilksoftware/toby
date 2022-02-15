<template>
  <div :class="{'relative pb-8': last}">
    <span
      v-if="last"
      class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200"
    />
    <div class="relative flex space-x-3">
      <div>
        <span :class="[statusInfo.outline.background, statusInfo.outline.foreground, 'h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white']">
          <component
            :is="statusInfo.outline.icon"
            class="w-5 h-5 text-white"
          />
        </span>
      </div>
      <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
        <div class="flex flex-col items-start">
          <div class="text-sm font-medium text-gray-700">
            {{ statusInfo.text }}
          </div>
          <div class="text-right text-sm whitespace-nowrap font-medium text-gray-400">
            {{ activity.user }}
          </div>
        </div>
        <div class="text-right text-sm whitespace-nowrap text-gray-500 flex flex-col">
          <time>{{ activity.date }}</time>
          <time>{{ activity.time }}</time>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import {computed} from 'vue'
import {useStatusInfo} from '@/Composables/statusInfo'

export default {
  name: 'VacationRequestActivity',
  props: {
    activity: {
      type: Object,
      default: () => null,
    },
    last: {
      type: Boolean,
      default: () => false,
    },
  },
  setup(props) {
    const statusInfo = computed(() => useStatusInfo(props.activity.state))

    return {
      statusInfo,
    }
  },
}
</script>
