<script setup>
import Popper from 'vue3-popper'

const emit = defineEmits(['changePreviousYear'])

defineProps({
  remaining: Number,
  year: Number,
  toNextYear: Number,
})
</script>

<template>
  <Popper
    open-delay="200"
    hover
    offset-distance="0"
  >
    <slot />
    <template #content="{ close }">
      <div class="py-2 px-6 text-left text-gray-900 whitespace-nowrap bg-white rounded-lg border border-gray-400">
        <dl class="divide-y divide-gray-200">
          <div class="py-2 space-y-1">
            <dt class="text-sm font-medium text-gray-500">
              Pozostałe dni
            </dt>
            <dd class="text-sm text-gray-900">
              {{ remaining ?? 0 }}
            </dd>
          </div>
          <div class="py-2 space-y-1">
            <dt class="text-sm font-medium text-gray-500">
              Przeniesione na {{ year + 1 }}
            </dt>
            <dd class="mt-1 text-sm text-gray-900">
              {{ toNextYear ?? 0 }}
            </dd>
          </div>
          <div class="py-2">
            <button
              type="button"
              class="inline-flex w-full justify-center py-2 px-4 text-sm font-medium text-white bg-blumilk-600 hover:bg-blumilk-700 rounded-md border border-transparent focus:outline-none focus:ring-2 focus:ring-blumilk-500 focus:ring-offset-2 shadow-sm"
              @click="close(); emit('changePreviousYear');"
            >
              Przenieś dni
            </button>
          </div>
        </dl>
      </div>
    </template>
  </Popper>
</template>
