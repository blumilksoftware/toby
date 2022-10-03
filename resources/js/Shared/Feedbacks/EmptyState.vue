<template>
  <div class="text-center my-5 text-gray-500">
    <slot name="head">
      <MagnifyingGlassIcon class="mx-auto w-12 h-12" />
    </slot>
    <h3 class="mt-2 text-sm font-medium">
      <slot name="title">
        No search result
      </slot>
    </h3>
    <p
      v-if="showDescription"
      class="text-sm"
    >
      <slot name="text">
        Try a different search query
      </slot>
    </p>
  </div>
</template>

<script setup>
import { MagnifyingGlassIcon } from '@heroicons/vue/24/solid'

defineProps({
  showDescription: {
    type: Boolean,
    default: true,
  },
})
</script>
