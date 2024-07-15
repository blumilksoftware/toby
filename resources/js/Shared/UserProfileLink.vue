<script setup>
import { usePage } from '@inertiajs/inertia-vue3'

defineProps({
  user: Object,
})

const auth =  usePage().props.value.auth
</script>

<template>
  <InertiaLink
    v-if="auth.can.manageUsers"
    :href="`/users/${user.id}`"
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
