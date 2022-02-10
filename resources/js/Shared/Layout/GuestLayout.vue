<template>
  <div class="min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8 bg-blumilk-25">
    <FlashMessage />
    <slot />
  </div>
</template>

<script>
import FlashMessage from '@/Shared/FlashMessage'
export default {
  name: 'GuestLayout',
  components: {FlashMessage},
}
</script>
