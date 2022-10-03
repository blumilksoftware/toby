<script setup>
import { DateTime } from 'luxon'

let props = defineProps({
  deployInformation: Object,
})

function getDate() {
  return DateTime.fromISO(props.deployInformation.release_created_at)
    .toLocaleString({
      year: 'numeric',
      month: '2-digit',
      day: '2-digit',
      hour: '2-digit',
      minute: '2-digit',
    })
}
</script>

<template>
  <div
    class="min-w-full p-4 text-xs text-gray-500 flex flex-row gap-x-1"
  >
    <p class="flex flex-col sm:flex-row gap-x-1">
      <span class="font-bold whitespace-nowrap">Wersja:</span>
      <span
        class="whitespace-nowrap"
        :title="`Commit: ${deployInformation.slug_commit ?? 'unset'}`"
      >
        {{ deployInformation.slug_description ?? 'unset' }} ({{ deployInformation.release_version ?? 'unset' }})
      </span>
    </p>
    <p
      v-if="deployInformation.release_created_at"
      class="flex flex-col sm:flex-row gap-x-1 border-l-2 border-gray-300 pl-1"
    >
      <span class="font-bold whitespace-nowrap">Ostatnio zbudowano:</span>
      <span>{{ getDate() }}</span>
    </p>
    <p
      v-if="deployInformation.github_url"
      class="flex flex-col sm:flex-row gap-x-1 border-l-2 border-gray-300 pl-1"
    >
      <span class="hidden sm:block font-bold whitespace-nowrap">Repozytorium:</span>
      <span class="block sm:hidden font-bold whitespace-nowrap">Repo:</span>
      <a
        v-if="deployInformation.github_url"
        class="text-gray-800 whitespace-nowrap underline hover:no-underline hover:text-blumilk-600"
        :href="deployInformation.github_url"
        target="_blank"
        rel="noopener nofollow noreferrer"
      >GitHub</a>
    </p>
  </div>
</template>
