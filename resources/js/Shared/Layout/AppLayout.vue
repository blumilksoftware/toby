<template>
  <div class="min-h-full">
    <MainMenu />
    <main class="lg:ml-64 flex flex-col flex-1 py-8">
      <SuccessAlert
        v-if="flash.success"
        :message="flash.success"
      />
      <ErrorAlert
        v-if="flash.error"
        :message="flash.error"
      />
      <div>
        <slot />
      </div>
    </main>
  </div>
</template>

<script>
import MainMenu from '@/Shared/MainMenu'
import SuccessAlert from '@/Shared/Alerts/SuccessAlert'
import ErrorAlert from '@/Shared/Alerts/ErrorAlert'

export default {
  name: 'AppLayout',
  components: {
    SuccessAlert,
    ErrorAlert,
    MainMenu,
  },
  props: {
    flash: {
      type: Object,
      default: () => null,
    },
  },
}
</script>
