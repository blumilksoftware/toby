<script setup>
import { computed } from 'vue'
import useVacationTypeInfo from '@/Composables/vacationTypeInfo.js'

const props = defineProps({
  type: String,
})

const { findType } = useVacationTypeInfo()

const vacationTypeInfo = computed(() => findType(props.type))
</script>

<template>
  <div class="flex items-center">
    <component
      :is="vacationTypeInfo.icon"
      :class="[vacationTypeInfo.color ,'w-5 h-5 mr-1.5']"
      :size="20"
    />
    <span>{{ vacationTypeInfo.text }}</span>
  </div>
</template>
