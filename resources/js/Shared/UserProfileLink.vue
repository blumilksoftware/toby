<script setup>
import InertiaLink from '@/Shared/InertiaLink.vue'
import { useGlobalProps } from '@/Composables/useGlobalProps.js'

defineProps({
  user: Object,
})

const { auth } = useGlobalProps()
</script>

<template>
  <InertiaLink
    v-if="!auth.can.manageUsers"
    :href="`/users/${user.id}`"
    @click.stop
  >
    <slot />
  </InertiaLink>
  <div
    v-else
    class="flex justify-start items-center"
  >
    <slot />
  </div>
</template>
