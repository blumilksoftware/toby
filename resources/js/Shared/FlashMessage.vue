<template>
  <transition
    enter-active-class="transform ease-out duration-300 transition"
    enter-from-class="opacity-0"
    enter-to-class="opacity-100"
    leave-active-class="transition ease-in duration-100"
    leave-from-class="opacity-100"
    leave-to-class="opacity-0"
  >
    <div
      v-if="$page.props.flash.success && show"
      class="fixed bottom-4 right-0 mx-auto w-full max-w-md bg-green-500 shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden z-50 sm:mr-4"
    >
      <div class="p-4">
        <div class="flex items-center">
          <div class="w-0 flex-1 flex justify-between">
            <CheckCircleIcon class="h-5 w-5 text-white mr-2" />
            <p class="w-0 flex-1 text-sm font-medium text-white">
              {{ $page.props.flash.success }}
            </p>
          </div>
          <div class="ml-4 flex-shrink-0 flex">
            <button
              class="bg-green-500 rounded-md inline-flex text-green-100 hover:text-green-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-600"
              @click="show = false"
            >
              <span class="sr-only">Close</span>
              <XIcon
                class="h-5 w-5"
                aria-hidden="true"
              />
            </button>
          </div>
        </div>
      </div>
    </div>
  </transition>
  <div
    v-if="($page.props.flash.error) && show"
    class="fixed bottom-4 right-0 mx-auto w-full max-w-md bg-red-500 shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden z-50 sm:mr-4"
  >
    <div class="p-4">
      <div class="flex items-center">
        <div class="w-0 flex-1 flex justify-between">
          <ExclamationIcon class="h-5 w-5 text-white mr-2" />
          <p class="w-0 flex-1 text-sm font-medium text-white">
            {{ $page.props.flash.error }}
          </p>
        </div>
        <div class="ml-4 flex-shrink-0 flex">
          <button
            class="bg-red-500 rounded-md inline-flex text-red-100 hover:text-red-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-600"
            @click="show = false"
          >
            <span class="sr-only">Close</span>
            <XIcon
              class="h-5 w-5"
              aria-hidden="true"
            />
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import {XIcon} from '@heroicons/vue/outline'
import {ExclamationIcon, CheckCircleIcon} from '@heroicons/vue/solid'

export default {
  name: 'FlashMessage',
  components: {
    CheckCircleIcon,
    ExclamationIcon,
    XIcon,
  },
  props: {
    flash: Object,
    default: () => ({success: null, error: null}),
  },
  data() {
    return {
      show:true,
    }
  },
  watch: {
    '$page.props.flash': {
      handler() {
        this.show = true
        setTimeout(() => this.show = false, 6000)
      },
    },
    deep: true,
  },
}
</script>
