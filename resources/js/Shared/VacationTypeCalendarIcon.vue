<script setup>
import { computed } from 'vue'
import useVacationTypeInfo from '@/Composables/vacationTypeInfo.js'

const props = defineProps({
  type: String,
  opacity: {
    type: Boolean,
    default: false,
  },
})

const { findType } = useVacationTypeInfo()

const typeInfo = computed(() => findType(props.type))
</script>

<template>
  <div class="flex items-center">
    <div>
      <span :class="[ opacity ? 'opacity-30': '' ,typeInfo.color, 'flex items-center justify-center']">
        <component :is="typeInfo.icon" />
      </span>
    </div>
  </div>
</template>
