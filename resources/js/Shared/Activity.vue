<template>
  <div :class="{'relative pb-8': last}">
    <span
      v-if="last"
      class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200"
    />
    <div class="relative flex space-x-3">
      <div>
        <span :class="[statusInfo.iconBackground, statusInfo.iconForeground, 'h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white']">
          <component
            :is="statusInfo.icon"
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
import {CheckIcon, ClockIcon, DocumentTextIcon, ThumbDownIcon, ThumbUpIcon, XIcon} from '@heroicons/vue/outline'
import {computed} from 'vue'

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
    const statuses = [
      {
        text: 'Utworzony',
        icon: DocumentTextIcon,
        value: 'created',
        iconForeground: 'text-white',
        iconBackground: 'bg-gray-400',
      },
      {
        text: 'Czeka na akceptację od technicznego',
        icon: ClockIcon,
        value: 'waiting_for_technical',
        iconForeground: 'text-white',
        iconBackground: 'bg-amber-400',
      },
      {
        text: 'Czeka na akceptację od administracyjnego',
        icon: ClockIcon,
        value: 'waiting_for_administrative',
        iconForeground: 'text-white',
        iconBackground: 'bg-amber-400',
      },
      {
        text: 'Odrzucony',
        icon: ThumbDownIcon,
        value: 'rejected',
        iconForeground: 'text-white',
        iconBackground: 'bg-rose-600',
      },
      {
        text: 'Zaakceptowany przez technicznego',
        icon: ThumbUpIcon,
        value: 'accepted_by_technical',
        iconForeground: 'text-white',
        iconBackground: 'bg-green-500',
      },
      {
        text: 'Zaakceptowany przez administracyjnego',
        icon: ThumbUpIcon,
        value: 'accepted_by_administrative',
        iconForeground: 'text-white',
        iconBackground: 'bg-green-500',
      },
      {
        text: 'Zatwierdzony',
        icon: CheckIcon,
        value: 'approved',
        iconForeground: 'text-white',
        iconBackground: 'bg-blumilk-500',
      },
      {
        text: 'Anulowany',
        icon: XIcon,
        value: 'canceled',
        iconForeground: 'text-white',
        iconBackground: 'bg-gray-900',
      },
    ]
    const statusInfo = computed(() => statuses.find(status => status.value === props.activity.state))

    return {
      statusInfo,
    }
  },
}
</script>