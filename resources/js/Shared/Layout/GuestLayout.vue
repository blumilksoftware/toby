<template>
  <div class="min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8 bg-blumilk-25">
    <ErrorAlert
      v-if="flash.error"
      :message="flash.error"
    />
    <slot />
  </div>
</template>

<script>
import ErrorAlert from '@/Shared/Alerts/ErrorAlert'
export default {
  name: 'GuestLayout',
  components: {ErrorAlert},
  props: {
    flash: {
      type: Object,
      default: () => null,
    },
  },
}
</script>
