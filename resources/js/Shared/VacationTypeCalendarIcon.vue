<template>
  <Popper hover>
    <div class="flex items-center">
      <div>
        <span :class="[typeInfo.outline.background, typeInfo.outline.foreground, 'flex items-center justify-center']">
          <component
            :is="typeInfo.outline.icon"
            :class="typeInfo.outline.background"
          />
        </span>
      </div>
    </div>
    <template #content>
      <div class="px-2 py-1 bg-white text-xs text-gray-900 shadow-md ">
        {{ typeInfo.text }}
      </div>
    </template>
  </Popper>
</template>

<script>
import {computed} from 'vue'
import {useVacationTypeInfo} from '@/Composables/vacationTypeInfo'
import Popper from 'vue3-popper'

export default {
  name: 'VacationTypeCalendarIcon',
  components: {
    Popper,
  },
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

    const typeInfo = computed(() => findType(props.type))

    return {
      typeInfo,
    }
  },
}
</script>
