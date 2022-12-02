<script setup>
import Popper from 'vue3-popper'

defineProps({
  limit: Number,
  days: Number,
  fromPreviousYear: Number,
  toNextYear: Number,
  year: Number,
})
</script>

<template>
  <Popper
    open-delay="200"
    hover
    offset-distance="0"
  >
    <slot />
    <template #content>
      <div class="py-2 px-6 text-left text-gray-900 whitespace-nowrap bg-white rounded-lg border border-gray-400">
        <dl class="divide-y divide-gray-200">
          <div class="py-2 space-y-1">
            <dt class="text-sm font-medium text-gray-500">
              Łączny limit
            </dt>
            <dd class="text-sm text-gray-900">
              {{ limit ?? 0 }}
            </dd>
          </div>
          <div class="py-2 space-y-1">
            <dt class="text-sm font-medium text-gray-500">
              Przysługujące dni
            </dt>
            <dd class="mt-1 text-sm text-gray-900">
              {{ days ?? 0 }}
            </dd>
          </div>
          <div class="py-2 space-y-1">
            <dt class="text-sm font-medium text-gray-500">
              Przeniesione dni z {{ year - 1 }}
            </dt>
            <dd class="mt-1 text-sm text-gray-900">
              {{ fromPreviousYear ?? 0 }}
            </dd>
          </div>
          <div class="py-2 space-y-1">
            <dt class="text-sm font-medium text-gray-500">
              Przeniesione dni na {{ year + 1 }}
            </dt>
            <dd class="mt-1 text-sm text-gray-900">
              {{ toNextYear ?? 0 }}
            </dd>
          </div>
        </dl>
      </div>
    </template>
  </Popper>
</template>
