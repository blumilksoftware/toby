<template>
  <section class="bg-white shadow-md">
    <div class="p-4 sm:px-6">
      <h2 class="text-lg font-medium leading-6 text-gray-900">
        Moje benefity
      </h2>
    </div>
    <div class="px-4 border-t border-gray-200 sm:px-6">
      <ul
        v-if="benefits.length"
        class="divide-y divide-gray-200"
      >
        <li
          v-for="benefit in benefits"
          :key="benefit.id"
          class="flex py-4"
        >
          <div>
            <p class="text-sm font-medium text-gray-900">
              {{ benefit.name }}
            </p>
            <div class="flex text-gray-600 text-sm">
              <div
                v-if="benefit.employee !== null"
              >
                Mój koszt: {{ new Intl.NumberFormat('pl-PL').format(benefit.employee/100) }} zł
              </div>
              <div
                v-else
              >
                Mój koszt: -
              </div>
            </div>
            <div class="flex text-gray-500 text-sm">
              <div
                v-if="benefit.employer !== null"
              >
                Koszt pracodawcy: {{ new Intl.NumberFormat('pl-PL').format(benefit.employer/100) }} zł
              </div>
              <div
                v-else
              >
                Koszt pracodawcy: -
              </div>
            </div>
          </div>
        </li>
      </ul>
      <EmptyState
        v-else
        :show-description="false"
      >
        <template #head>
          <GiftIcon class="mx-auto w-12 h-12" />
        </template>
        <template #title>
          Brak benetifów
        </template>
      </EmptyState>
    </div>
  </section>
</template>

<script setup>
import EmptyState from '@/Shared/Feedbacks/EmptyState.vue'
import { GiftIcon } from '@heroicons/vue/solid'

defineProps({
  benefits: Object,
})
</script>
