<script setup>
import MainMenu from '@/Shared/MainMenu.vue'
import { useToast } from 'vue-toastification'
import { watch } from 'vue'
import DeployInfo from '@/Shared/DeployInfo.vue'

const props = defineProps({
  flash: Object,
  auth: Object,
  years: Object,
  vacationRequestsCount: Number,
  deployInformation: Object,
})

const toast = useToast()

watch(() => props.flash, flash => {
  if (flash.success) {
    toast.success(flash.success)
  }

  if (flash.info) {
    toast.info(flash.info)
  }

  if (flash.error) {
    toast.error(flash.error)
  }
}, { immediate: true })
</script>

<template>
  <div class="relative min-h-screen">
    <MainMenu
      :auth="auth"
      :years="years"
      :vacation-requests-count="vacationRequestsCount"
    />
    <main class="flex flex-col flex-1 py-8 lg:ml-60">
      <div class="lg:px-4">
        <slot />
      </div>
      <div class="h-8 sm:h-4" />
    </main>
    <DeployInfo
      :deploy-information="deployInformation"
      class="absolute bottom-0 justify-end"
    />
  </div>
</template>
