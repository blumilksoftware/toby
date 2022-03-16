<template>
  <div class="flex items-center">
    <component
      :is="vacationTypeInfo.solid.icon"
      :class="[vacationTypeInfo.solid.color ,'w-5 h-5 mr-1.5']"
      :size="20"
    />
    <span>{{ vacationTypeInfo.text }}</span>
  </div>
</template>

<script>
import {computed} from 'vue'
import {useVacationTypeInfo} from '@/Composables/vacationTypeInfo'

export default {
  name: 'VacationType',
  props: {
    type: {
      type: String,
      default: () => null,
    },
    last: {
      type: Boolean,
      default: () => false,
    },
  },
  setup(props) {
    const { findType } = useVacationTypeInfo()

    const vacationTypeInfo = computed(() => findType(props.type))

    return {
      vacationTypeInfo,
    }
  },
}
</script>
