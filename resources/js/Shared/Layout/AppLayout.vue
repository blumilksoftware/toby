<script setup>
import MainMenu from '@/Shared/MainMenu.vue'
import LastUpdate from '@/Shared/LastUpdate.vue'
import { useToast } from 'vue-toastification'
import { computed, ref, watch } from 'vue'
import DeployInfo from '@/Shared/DeployInfo.vue'
import { updateFavicon } from '@/Shared/updateFavicon'
import { Head } from '@inertiajs/vue3'
import { useGlobalProps } from '@/Composables/useGlobalProps'

const toast = useToast()

defineProps({
  title: String,
})

const {
  deployInformation,
  flash,
  auth,
  lastUpdate,
} = useGlobalProps()

const lastUpdateValue = ref(lastUpdate.value)

watch(flash, value => {
  if (value.success) {
    toast.success(value.success)
  }

  if (value.info) {
    toast.info(value.info)
  }

  if (value.error) {
    toast.error(value.error)
  }
}, { immediate: true })

const isUpdated = computed(() => lastUpdate.value !== lastUpdateValue.value)

watch(isUpdated, (value) => updateFavicon(value ? '/images/icon-alert.png' : '/images/icon.png'), { immediate: true })
</script>

<template>
  <Head :title="title" />
  <LastUpdate
    v-if="auth.can.listAllRequests"
    @last-update-updated="lastUpdateValue = $event"
  />
  <div class="relative min-h-screen">
    <MainMenu
      :show-refresh-button="isUpdated && auth.can.listAllRequests"
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
