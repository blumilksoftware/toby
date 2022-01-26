<template>
  <div class="min-h-full">
    <MainMenu />
    <main class="-mt-24 pb-8">
      <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:max-w-7xl lg:px-8">
        <slot />
      </div>
    </main>
  </div>
</template>

<script>
import MainMenu from '@/Shared/MainMenu'

export default {
  name: 'AppLayout',
  components: {MainMenu},
}
</script>
