<template>
  <section class="bg-white shadow-md">
    <TabGroup>
      <div class="w-100 p-4 sm:px-6 border-b border-gray-200 flex items-center">
        <h2 class="text-lg font-medium leading-6 text-gray-900 w-full">
          Urodziny
        </h2>
      </div>
      <TabList class="w-100 flex">
        <Tab
          v-slot="{ selected }"
          class="w-1/2 h-auto"
          as="template"
        >
          <div
            class="w-100 border-b border-gray-200 p-2 sm:px-6 cursor-pointer flex items-center focus:outline-none"
            :class="[selected ? 'border-b-2 border-b-blumilk-500 text-blumilk-600' : 'border-b-2 border-b-white']"
          >
            <h3 class="text-md font-medium leading-6 w-full">
              Dzisiaj
            </h3>
          </div>
        </Tab>
        <Tab
          v-slot="{ selected }"
          class="w-1/2 h-auto"
          as="template"
        >
          <div
            class="w-100 border-b border-gray-200 p-2 sm:px-6 cursor-pointer flex items-center focus:outline-none"
            :class="[selected ? 'border-b-2 border-b-blumilk-500 text-blumilk-600' : 'border-b-2 border-b-white']"
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
            v-if="birthdays.length"
            class="divide-y divide-gray-200"
          >
            <li
              v-for="day in birthdays"
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
              <CakeIcon class="h-12 w-12 mx-auto" />
            </template>
            <template #title>
              Nikt nie ma urodzin
            </template>
          </EmptyState>
        </TabPanel>
        <TabPanel class="px-4 border-t border-gray-200 sm:px-6">
          <ul
            v-if="upcomingBirthdays.length"
            class="divide-y divide-gray-200"
          >
            <li
              v-for="day in upcomingBirthdays"
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
                <p class="text-sm text-gray-500">
                  {{ day.displayDate }} - {{ day.relativeDate }}
                </p>
              </div>
            </li>
          </ul>
          <EmptyState
            v-else
            :show-description="false"
          >
            <template #head>
              <CakeIcon class="h-12 w-12 mx-auto" />
            </template>
            <template #title>
              Brak nadchodzących urodzin
            </template>
          </EmptyState>
        </TabPanel>
      </TabPanels>
    </TabGroup>
  </section>
</template>

<script setup>
import EmptyState from '@/Shared/Feedbacks/EmptyState.vue'
import { CakeIcon } from '@heroicons/vue/outline'
import { TabGroup, TabList, Tab, TabPanels, TabPanel } from '@headlessui/vue'

defineProps({
  birthdays: Object,
  upcomingBirthdays: Object,
})
</script>
