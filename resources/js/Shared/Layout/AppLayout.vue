<script setup>
import MainMenu from '@/Shared/MainMenu.vue'
import { useToast } from 'vue-toastification'
import {onMounted, ref, watch} from 'vue'
import DeployInfo from '@/Shared/DeployInfo.vue'
import axios from "axios";

const props = defineProps({
  flash: Object,
  auth: Object,
  years: Object,
  vacationRequestsCount: Number,
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

const fetchLastUpdate = async () => {
  try {
    const response = await axios.get('/api/last-update')
    if (response.data.lastUpdate !== props.lastUpdate) {
      isUpdated.value = true
      updateFavicon("/images/icon-alert.png")
    }
  } catch (error) {
    console.error('Failed to fetch last update:', error)
  }
}

onMounted(() => {
  fetchLastUpdate()
  setInterval(fetchLastUpdate, 5000)
})

// onUnmounted(() => {
//   updateFavicon("/images/icon.png")
// })

function updateFavicon(iconUrl) {
  var link = document.querySelector("link[rel*='icon']") || document.createElement("link");
  link.type = "image/x-icon";
  link.rel = "shortcut icon";
  link.href = iconUrl;
  document.getElementsByTagName("head")[0].appendChild(link);
}
</script>

<template>
  <div class="relative min-h-screen">
    <MainMenu
      :auth="auth"
      :years="years"
      :vacation-requests-count="vacationRequestsCount"
      :show-refresh-button="isUpdated"
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
