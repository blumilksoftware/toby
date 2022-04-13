<template>
  <InertiaLink
    as="button"
    :onStart="() => processing = true"
    :onFinish="() => processing = false"
    :onBefore="() => ! processing"
  >
    <slot />
  </InertiaLink>
</template>

<script setup>
import { ref, watch } from 'vue'

let processing = ref(false)

watch(processing, (value) => console.log(value))
</script>
