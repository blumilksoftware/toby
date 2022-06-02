<template>
  <div class="text-center my-5">
    <slot name="head">
      <SearchIcon class="mx-auto w-12 h-12" />
    </slot>
    <h3 class="mt-2 text-sm font-medium">
      <slot name="title">
        No search result
      </slot>
    </h3>
    <p class="text-sm text-gray-500">
      <slot name="text">
        Try a different search query
      </slot>
    </p>
  </div>
</template>

<script setup>
import { SearchIcon } from '@heroicons/vue/solid'
</script>
