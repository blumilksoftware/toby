<template>
  <div class="min-h-full">
    <MainMenu />
    <main class="lg:ml-64 flex flex-col flex-1 py-8">
      <div>
        <slot />
      </div>
    </main>
  </div>
</template>

<script>
import MainMenu from '@/Shared/MainMenu'
import {useToast} from 'vue-toastification'
import {watch} from 'vue'

export default {
  name: 'AppLayout',
  components: {
    MainMenu,
  },
  props: {
    flash: {
      type: Object,
      default: () => null,
    },
  },
  setup(props) {
    const toast = useToast()

    watch(() => props.flash, flash => {
      if (flash.success) {
        toast.success(flash.success)
      }

      if (flash.error) {
        toast.error(flash.error)
      }
    }, {immediate:true})

    return {
      toast,
    }
  },
}
</script>
