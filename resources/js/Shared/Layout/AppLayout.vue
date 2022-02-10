<template>
  <div class="min-h-full">
    <MainMenu />
    <main class="lg:ml-64 flex flex-col flex-1 py-8">
      <FlashMessage />
      <div>
        <slot />
      </div>
    </main>
  </div>
</template>

<script>
import MainMenu from '@/Shared/MainMenu'
import FlashMessage from '@/Shared/FlashMessage'

export default {
  name: 'AppLayout',
  components: {
    FlashMessage,
    MainMenu,
  },
}
</script>
