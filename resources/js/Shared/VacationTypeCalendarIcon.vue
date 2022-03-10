<template>
  <div class="flex items-center">
    <div>
      <span :class="[statusInfo.outline.background, statusInfo.outline.foreground, 'flex items-center justify-center']">
        <component
          :is="statusInfo.outline.icon"
          :class="statusInfo.outline.background"
        />
      </span>
    </div>
  </div>
</template>

<script>
import {computed} from 'vue'
import {useVacationTypeInfo} from '@/Composables/vacationTypeInfo'

export default {
  name: 'VacationTypeCalendarIcon',
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
    const { findStatus } = useVacationTypeInfo()

    const statusInfo = computed(() => findStatus(props.status))

    return {
      statusInfo,
    }
  },
}
</script>
