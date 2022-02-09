<template>
  <div class="flex items-center">
    <component
      :is="statusInfo.icon"
      :class="[statusInfo.color ,'w-5 h-5 mr-1']"
    />
    <span>{{ statusInfo.text }}</span>
  </div>
</template>

<script>
import {CheckIcon, ClockIcon, DocumentTextIcon, ThumbDownIcon, ThumbUpIcon, XIcon} from '@heroicons/vue/solid'
import {computed} from 'vue'

export default {
  name: 'VacationRequestStatus',
  props: {
    status: {
      type: String,
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
        color: 'text-gray-400',
      },
      {
        text: 'Czeka na akceptację od technicznego',
        icon: ClockIcon,
        value: 'waiting_for_technical',
        color: 'text-amber-400',
      },
      {
        text: 'Czeka na akceptację od administracyjnego',
        icon: ClockIcon,
        value: 'waiting_for_administrative',
        color: 'text-amber-400',
      },
      {
        text: 'Odrzucony',
        icon: ThumbDownIcon,
        value: 'rejected',
        color: 'text-rose-600',
      },
      {
        text: 'Zaakceptowany przez technicznego',
        icon: ThumbUpIcon,
        value: 'accepted_by_technical',
        color: 'text-green-500',
      },
      {
        text: 'Zaakceptowany przez administracyjnego',
        icon: ThumbUpIcon,
        value: 'accepted_by_administrative',
        color: 'text-green-500',
      },
      {
        text: 'Zatwierdzony',
        icon: CheckIcon,
        value: 'approved',
        color: 'text-blumilk-500',
      },
      {
        text: 'Anulowany',
        icon: XIcon,
        value: 'canceled',
        color: 'text-gray-900',
      },
    ]
    const statusInfo = computed(() => statuses.find(status => status.value === props.status))

    return {
      statusInfo,
    }
  },
}
</script>
