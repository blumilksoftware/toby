<template>
  <div
    :class="{ 'flex-col': isVertical(), 'flex-row': isHorizontal() }"
    class="min-w-full p-4 text-xs text-gray-500 flex gap-x-1"
  >
    <p class="flex flex-row gap-x-1">
      <strong>Wydanie:</strong>
      <a
        v-if="deployInformation.github_url"
        class="text-blumilk-600 hover:underline hover:text-blumilk-500"
        :href="`${deployInformation.github_url}/commit/${deployInformation.slug_commit}`"
        target="_blank"
        rel="noopener nofollow noreferrer"
        :title="`Commit: ${deployInformation.slug_commit ?? 'unset'}`"
      >
        {{ deployInformation.slug_description ?? 'unset' }} ({{ deployInformation.release_version }})
      </a>
      <span
        v-else
        :title="`Commit: ${deployInformation.slug_commit ?? 'unset'}`"
      >
        {{ deployInformation.slug_description ?? 'unset' }} ({{ deployInformation.release_version ?? 'unset' }})
      </span>
    </p>
    <p
      v-if="deployInformation.release_created_at"
      class="flex flex-row gap-x-1"
      :class="{ 'border-l-2 border-gray-300 pl-1': isHorizontal() }"
    >
      <strong>Data wydania:</strong>
      <span>{{ DateTime.fromISO(deployInformation.release_created_at).toLocaleString(DateTime.DATETIME_SHORT) }}</span>
    </p>
    <p
      v-if="deployInformation.github_url"
      :class="{ 'border-l-2 border-gray-300 pl-1': isHorizontal(), 'flex flex-row gap-x-1': isVertical() }"
    >
      <strong v-if="isVertical()">Repozytorium:</strong>
      <a
        v-if="deployInformation.github_url"
        class="text-blumilk-600 hover:underline hover:text-blumilk-500"
        :href="deployInformation.github_url"
        target="_blank"
        rel="noopener nofollow noreferrer"
      >GitHub</a>
    </p>
  </div>
</template>

<script setup>
import { DateTime } from 'luxon'

let props = defineProps({
  deployInformation: Object,
  layout: {
    type: String,
    default: 'horizontal',
  },
})

function isVertical() {
  return props.layout === 'vertical'
}

function isHorizontal() {
  return props.layout === 'horizontal'
}
</script>
