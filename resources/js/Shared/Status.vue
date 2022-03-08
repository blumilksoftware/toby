<template>
  <div class="flex items-center">
    <component
      :is="statusInfo.solid.icon"
      :class="[statusInfo.solid.color ,'w-5 h-5 mr-1']"
    />
    <span>{{ statusInfo.text }}</span>
  </div>
</template>

<script>
import {computed} from 'vue'
import {useStatusInfo} from '@/Composables/statusInfo'

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
    const { findStatus } = useStatusInfo()

    const statusInfo = computed(() => findStatus(props.status))

    return {
      statusInfo,
    }
  },
}
</script>
