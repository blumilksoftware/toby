<script setup>
import EmptyState from '@/Shared/Feedbacks/EmptyState.vue'
import { CakeIcon } from '@heroicons/vue/24/outline'
import UserProfileLink from '@/Shared/UserProfileLink.vue'

defineProps({
  birthdays: Object,
})
</script>

<template>
  <section class="bg-white shadow-md">
    <div class="w-100 p-4 sm:px-6 border-b border-gray-200 flex items-center">
      <h2 class="text-lg font-medium leading-6 text-gray-900 w-full">
        Urodziny
      </h2>
    </div>
    <div class="px-4 border-t border-gray-200 sm:px-6">
      <ul
        v-if="birthdays.length"
        class="divide-y divide-gray-200"
      >
        <li
          v-for="day in birthdays"
          :key="day.user.id"
          class="flex py-4"
        >
          <UserProfileLink
            :user="day.user"
            class="flex"
          >
            <img
              :src="day.user.avatar"
              class="size-10 rounded-full"
            >
            <div class="ml-3">
              <p class="text-sm font-medium text-gray-900">
                {{ day.user.name }}
              </p>
              <p class="text-sm text-gray-500">
                {{ day.user.email }}
              </p>
              <p class="text-sm text-gray-500">
                {{ day.displayDate }} - {{ day.relativeDate }}
              </p>
            </div>
          </UserProfileLink>
        </li>
      </ul>
      <EmptyState
        v-else
        :show-description="false"
      >
        <template #head>
          <CakeIcon class="size-12 mx-auto" />
        </template>
        <template #title>
          Brak nadchodzących urodzin
        </template>
      </EmptyState>
    </div>
  </section>
</template>
