<script setup>
import { computed } from 'vue'
import { useStatusInfo } from '@/Composables/statusInfo.js'

const props = defineProps({
  status: String,
})

const { findStatus } = useStatusInfo()

const statusInfo = computed(() => findStatus(props.status))
</script>

<template>
  <div class="flex items-center">
    <component
      :is="statusInfo.solid.icon"
      :class="[statusInfo.solid.color, 'size-5 mr-1']"
    />
    <span>{{ statusInfo.text }}</span>
  </div>
</template>
