<template>
  <div class="relative flex flex-col justify-center py-12 min-h-screen bg-blumilk-25 sm:px-6 lg:px-8">
    <slot />
    <div class="absolute left-0 bottom-0 min-w-full p-4 text-xs text-gray-500 flex flex-col gap-x-1">
      <p>
        <strong>Wydanie:</strong> <a
          v-if="deployInformation.github_url"
          :href="`${deployInformation.github_url}/commit/${deployInformation.slug_commit}`"
          target="_blank"
          rel="noopener nofollow noreferrer"
          :title="`Commit: ${deployInformation.slug_commit}`"
        >
          {{ deployInformation.slug_description }} ({{ deployInformation.release_version }})
        </a>
        <span
          v-else
          :title="`Commit: ${deployInformation.slug_commit}`"
        >
          {{ deployInformation.slug_description }}
        </span>
      </p>
      <p v-if="deployInformation.release_created_at">
        <strong>Data wydania:</strong> {{ deployInformation.release_created_at }}
      </p>
    </div>
  </div>
</template>
<script setup>
defineProps({
  deployInformation: Object,
})
</script>
