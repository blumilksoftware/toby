<template>
  <Popper hover>
    <div class="flex items-center">
      <div>
        <span :class="[typeInfo.color, 'flex items-center justify-center']">
          <component :is="typeInfo.icon" />
        </span>
      </div>
    </div>
    <template #content>
      <div class="py-2 px-4 text-xs text-gray-900 bg-white rounded-lg border border-gray-400 ">
        {{ typeInfo.text }}
      </div>
    </template>
  </Popper>
</template>

<script setup>
import { computed } from 'vue'
import useVacationTypeInfo from '@/Composables/vacationTypeInfo'
import Popper from 'vue3-popper'

const props = defineProps({
  type: String,
})

const { findType } = useVacationTypeInfo()

const typeInfo = computed(() => findType(props.type))
</script>
