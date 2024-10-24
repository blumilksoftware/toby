<script setup>
import { computed } from 'vue'
import useSettlementTypeInfo from '@/Composables/settlementTypeInfo.js'

const props = defineProps({
  type: String,
})

const { findType } = useSettlementTypeInfo()

const settlementTypeInfo = computed(() => findType(props.type))
</script>

<template>
  <div class="flex items-center">
    <component
      :is="settlementTypeInfo.icon"
      :class="[settlementTypeInfo.color ,'size-5 mr-1.5']"
      :size="20"
    />
    <span>{{ settlementTypeInfo.text }}</span>
  </div>
</template>
