<script setup>
import MainMenu from '@/Shared/MainMenu.vue'
import LastUpdate from '@/Shared/LastUpdate.vue'
import { useToast } from 'vue-toastification'
import { ref, watch } from 'vue'
import DeployInfo from '@/Shared/DeployInfo.vue'
import { updateFavicon } from '@/Shared/updateFavicon'

const props = defineProps({
  flash: Object,
  auth: Object,
  vacationRequestsCount: Number,
  overtimeRequestsCount: Number,
  deployInformation: Object,
  lastUpdate: String,
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

const isUpdated = ref(false)

function vacationPageOpened() {
  isUpdated.value = false
  updateFavicon('/images/icon.png')
}
</script>

<template>
  <LastUpdate
    v-if="props.auth.can.listAllRequests"
    :is-updated="isUpdated"
    :last-update="lastUpdate"
    @last-update-updated="isUpdated = true"
  />
  <div class="relative min-h-screen">
    <MainMenu
      :auth="auth"
      :show-refresh-button="isUpdated"
      :vacation-requests-count="vacationRequestsCount"
      :overtime-requests-count="overtimeRequestsCount"
      @open="vacationPageOpened"
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
