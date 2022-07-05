<template>
  <div class="relative flex flex-col justify-center py-12 min-h-screen bg-blumilk-25 sm:px-6 lg:px-8">
    <slot />
    <DeployInfo
      :deploy-information="deployInformation"
      layout="vertical"
      class="absolute right-0 bottom-0"
    />
  </div>
</template>

<script setup>
import DeployInfo from '@/Shared/DeployInfo'

defineProps({
  deployInformation: Object,
})
</script>
