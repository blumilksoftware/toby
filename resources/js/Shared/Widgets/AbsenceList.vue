<template>
  <section class="bg-white shadow-md">
    <div class="p-4 sm:px-6">
      <h2 class="text-lg font-medium leading-6 text-gray-900">
        Dzisiejsze nieobecności
      </h2>
    </div>
    <div class="px-4 border-t border-gray-200 sm:px-6">
      <ul
        v-if="absences.length"
        class="divide-y divide-gray-200"
      >
        <li
          v-for="absence in absences"
          :key="absence.user.id"
          class="flex py-4"
        >
          <img
            class="w-10 h-10 rounded-full"
            :src="absence.user.avatar"
          >
          <div class="ml-3">
            <p class="text-sm font-medium text-gray-900">
              {{ absence.user.name }}
            </p>
            <p class="text-sm text-gray-500">
              {{ absence.user.email }}
            </p>
          </div>
        </li>
      </ul>
      <EmptyState
        v-else
        :show-description="false"
      >
        <template #head>
          <SunIcon class="mx-auto w-12 h-12" />
        </template>
        <template #title>
          Brak nieobecności
        </template>
      </EmptyState>
    </div>
  </section>
</template>

<script setup>
import EmptyState from '@/Shared/Feedbacks/EmptyState'
import { SunIcon } from '@heroicons/vue/solid'

defineProps({
  absences: Object,
})
</script>
