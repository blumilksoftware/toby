<template>
  <section class="bg-white shadow-md">
    <div class="p-4 sm:px-6">
      <h2 class="text-lg font-medium leading-6 text-gray-900">
        Dzisiejsza praca zdalna
      </h2>
    </div>
    <div class="px-4 border-t border-gray-200 sm:px-6">
      <ul
        v-if="remoteDays.length"
        class="divide-y divide-gray-200"
      >
        <li
          v-for="day in remoteDays"
          :key="day.user.id"
          class="flex py-4"
        >
          <img
            class="w-10 h-10 rounded-full"
            :src="day.user.avatar"
          >
          <div class="ml-3">
            <p class="text-sm font-medium text-gray-900">
              {{ day.user.name }}
            </p>
            <p class="text-sm text-gray-500">
              {{ day.user.email }}
            </p>
          </div>
        </li>
      </ul>
      <EmptyState
        v-else
        :show-description="false"
      >
        <template #head>
          <HomeCityIcon
            class="flex justify-center"
            size="48"
          />
        </template>
        <template #title>
          Nikt nie pracuje zdalnie
        </template>
      </EmptyState>
    </div>
  </section>
</template>

<script setup>
import EmptyState from '@/Shared/Feedbacks/EmptyState'
import HomeCityIcon from 'vue-material-design-icons/HomeCity'

defineProps({
  remoteDays: Object,
})
</script>
