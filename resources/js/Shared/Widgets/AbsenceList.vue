<script setup>
import EmptyState from '@/Shared/Feedbacks/EmptyState.vue'
import { SunIcon } from '@heroicons/vue/24/solid'
import { Tab, TabGroup, TabList, TabPanel, TabPanels } from '@headlessui/vue'
import UserProfileLink from '@/Shared/UserProfileLink.vue'

defineProps({
  absences: Object,
  upcomingAbsences: Object,
})
</script>

<template>
  <section class="bg-white shadow-md">
    <TabGroup>
      <div class="w-100 p-4 sm:px-6 border-b border-gray-200 flex items-center">
        <h2 class="text-lg font-medium leading-6 text-gray-900 w-full">
          Nieobecności
        </h2>
      </div>
      <TabList class="w-100 flex">
        <Tab
          v-slot="{ selected }"
          as="template"
          class="w-1/2 h-auto"
        >
          <div
            :class="[selected ? 'border-b-2 border-b-blumilk-500 text-blumilk-600' : 'border-b-2 border-b-white']"
            class="w-100 border-b border-gray-200 p-2 sm:px-6 cursor-pointer flex items-center focus:outline-none"
          >
            <h3 class="text-md font-medium leading-6 w-full">
              Aktualne
            </h3>
          </div>
        </Tab>
        <Tab
          v-slot="{ selected }"
          as="template"
          class="w-1/2 h-auto"
        >
          <div
            :class="[selected ? 'border-b-2 border-b-blumilk-500 text-blumilk-600' : 'border-b-2 border-b-white']"
            class="w-100 border-b border-gray-200 p-2 sm:px-6 cursor-pointer flex items-center focus:outline-none"
          >
            <h3 class="text-md font-medium leading-6 w-full">
              Nadchodzące
            </h3>
          </div>
        </Tab>
      </TabList>
      <TabPanels>
        <TabPanel class="px-4 border-t border-gray-200 sm:px-6">
          <ul
            v-if="absences.length"
            class="divide-y divide-gray-200"
          >
            <li
              v-for="day in absences"
              :key="day.id"
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
                    {{ day.displayDate }} <span v-if="day.pending">(oczekujące)</span>
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
              <SunIcon class="mx-auto size-12" />
            </template>
            <template #title>
              Brak nieobecności
            </template>
          </EmptyState>
        </TabPanel>
        <TabPanel class="px-4 border-t border-gray-200 sm:px-6">
          <ul
            v-if="upcomingAbsences.length"
            class="divide-y divide-gray-200"
          >
            <li
              v-for="day in upcomingAbsences"
              :key="day.user.id"
              class="flex py-4"
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
                  {{ day.displayDate }} <span v-if="day.pending">(oczekujące)</span>
                </p>
              </div>
            </li>
          </ul>
          <EmptyState
            v-else
            :show-description="false"
          >
            <template #head>
              <SunIcon class="mx-auto size-12" />
            </template>
            <template #title>
              Brak nieobecności
            </template>
          </EmptyState>
        </TabPanel>
      </TabPanels>
    </TabGroup>
  </section>
</template>
