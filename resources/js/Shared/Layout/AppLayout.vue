<template>
  <div class="min-h-full">
    <MainMenu
      :auth="auth"
      :years="years"
      :vacation-requests-count="vacationRequestsCount"
    />
    <main class="flex flex-col flex-1 py-8 lg:ml-64">
      <div class="lg:px-4">
        <slot />
      </div>
      <div class="p-4 text-xs text-gray-500 flex align-baseline gap-x-1">
        <p>
          <strong>Wydanie:</strong> <a
            v-if="deployInformation.github_url"
            :href="`${deployInformation.github_url}/commit/${deployInformation.slug_commit}`"
            target="_blank"
            rel="noopener nofollow noreferrer"
            :title="`Commit: ${deployInformation.slug_commit}`"
          >
            {{ deployInformation.slug_description }} ({{ deployInformation.release_version }})
          </a><span
            v-else
            :title="`Commit: ${deployInformation.slug_commit}`"
          >
            {{ deployInformation.slug_description }}
          </span>
        </p>
        <p
          v-if="deployInformation.release_created_at"
          class="border-l-2 border-gray-300 pl-1"
        >
          <strong>Data wydania:</strong> {{ deployInformation.release_created_at }}
        </p>
      </div>
    </main>
  </div>
</template>

<script setup>
import MainMenu from '@/Shared/MainMenu'
import { useToast } from 'vue-toastification'
import { watch } from 'vue'

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
}, { immediate:true })
</script>
