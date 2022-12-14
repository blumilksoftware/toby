<script setup>
defineProps({
  deployInformation: Object,
})
</script>

<template>
  <div
    class="min-w-full p-4 text-xs text-gray-500 flex flex-row gap-x-1"
  >
    <p class="flex flex-col sm:flex-row gap-x-1">
      <span class="font-bold whitespace-nowrap">Wersja: </span>
      <span>{{ deployInformation.version }}</span>
    </p>
    <p
      class="flex flex-col sm:flex-row gap-x-1 border-l-2 border-gray-300 pl-1"
    >
      <span class="font-bold whitespace-nowrap">Ostatnio zbudowano: </span>
      <span>{{ deployInformation.date }}</span>
    </p>
    <p
      class="flex flex-col sm:flex-row gap-x-1 border-l-2 border-gray-300 pl-1"
    >
      <a
        class="text-gray-800 whitespace-nowrap underline hover:no-underline hover:text-blumilk-600"
        href="https://github.com/blumilksoftware/toby"
        target="_blank"
        rel="noopener nofollow noreferrer"
      >GitHub</a>
    </p>
  </div>
</template>
