<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
  href: {
    type: String,
    default: null,
  },
})
</script>

<template>
  <Link :href="href">
    <slot />
  </Link>
</template>
