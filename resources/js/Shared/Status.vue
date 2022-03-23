<template>
  <div class="flex items-center">
    <component
      :is="statusInfo.solid.icon"
      :class="[statusInfo.solid.color, 'w-5 h-5 mr-1']"
    />
    <span>{{ statusInfo.text }}</span>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useStatusInfo } from '@/Composables/statusInfo'

const props = defineProps({
  status: String,
})

const { findStatus } = useStatusInfo()

const statusInfo = computed(() => findStatus(props.status))
</script>
