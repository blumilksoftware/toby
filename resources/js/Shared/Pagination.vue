<template>
  <div
    v-if="pagination.last_page !== 1"
    class="flex justify-between items-center py-3 px-4 bg-white rounded-b-lg border-t border-gray-200 sm:px-6"
  >
    <div class="flex flex-1 justify-between sm:hidden">
      <Component
        :is="prevLink ? 'InertiaLink': 'span'"
        :href="prevLink"
        class="inline-flex relative items-center py-2 px-4 text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 rounded-md border border-gray-300"
      >
        Poprzednia
      </Component>
      <Component
        :is="nextLink ? 'InertiaLink': 'span'"
        :href="nextLink"
        class="inline-flex relative items-center py-2 px-4 ml-3 text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 rounded-md border border-gray-300"
      >
        Następna
      </Component>
    </div>
    <div class="hidden sm:flex sm:flex-1 sm:justify-between sm:items-center">
      <div class="text-sm text-gray-700">
        Wyświetlanie od
        <span class="font-medium">{{ pagination.from }}</span>
        do
        <span class="font-medium">{{ pagination.to }}</span>
        z
        <span class="font-medium">{{ pagination.total }}</span>
        wyników
      </div>
      <nav class="inline-flex relative z-0 space-x-1">
        <template
          v-for="(link, index) in pagination.links"
          :key="index"
        >
          <Component
            :is="link.url ? 'InertiaLink' : 'span'"
            :href="link.url"
            :preserve-scroll="true"
            class="inline-flex relative items-center py-2 px-4 text-sm font-medium rounded-md border"
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
