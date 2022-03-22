<template>
  <div
    v-if="pagination.last_page !== 1"
    class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6 rounded-b-lg"
  >
    <div class="flex-1 flex justify-between sm:hidden">
      <Component
        :is="prevLink ? 'InertiaLink': 'span'"
        :href="prevLink"
        class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
      >
        Poprzednia
      </Component>
      <Component
        :is="nextLink ? 'InertiaLink': 'span'"
        :href="nextLink"
        class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
      >
        Następna
      </Component>
    </div>
    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
      <div class="text-sm text-gray-700">
        Wyświetlanie
        <span class="font-medium">{{ pagination.from }}</span>
        od
        <span class="font-medium">{{ pagination.to }}</span>
        do
        <span class="font-medium">{{ pagination.total }}</span>
        wyników
      </div>
      <nav class="relative z-0 inline-flex space-x-1">
        <template
          v-for="(link, index) in pagination.links"
          :key="index"
        >
          <Component
            :is="link.url ? 'InertiaLink' : 'span'"
            :href="link.url"
            :preserve-scroll="true"
            class="relative inline-flex items-center px-4 py-2 border rounded-md text-sm font-medium"
            :class="{ 'z-10 bg-blumilk-25 border-blumilk-500 text-blumilk-600': link.active, 'bg-white border-gray-300 text-gray-500': !link.active, 'hover:bg-blumilk-25': link.url, 'border-none': !link.url}"
            v-text="link.label"
          />
        </template>
      </nav>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  pagination: Object,
})

const prevLink = computed(() => props.pagination.links.at(0)?.url)
const nextLink = computed(() => props.pagination.links.at(-1)?.url)

</script>
