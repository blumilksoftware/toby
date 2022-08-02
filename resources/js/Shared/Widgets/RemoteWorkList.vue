<template>
  <section class="bg-white shadow-md">
    <TabGroup>
      <div class="w-100 p-4 sm:px-6 border-b border-gray-200 flex items-center">
        <h2 class="text-lg font-medium leading-6 text-gray-900 w-full">
          Praca zdalna
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
              Aktualna
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
              Nadchodząca
            </h3>
          </div>
        </Tab>
      </TabList>
      <TabPanels>
        <TabPanel class="px-4 border-t border-gray-200 sm:px-6">
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
                <p class="text-sm text-gray-500">
                  {{ day.displayDate }}
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
                :size="48"
              />
            </template>
            <template #title>
              Nikt nie pracuje zdalnie
            </template>
          </EmptyState>
        </TabPanel>
        <TabPanel class="px-4 border-t border-gray-200 sm:px-6">
          <ul
            v-if="upcomingRemoteDays.length"
            class="divide-y divide-gray-200"
          >
            <li
              v-for="day in upcomingRemoteDays"
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
                  {{ day.displayDate }}
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
                :size="48"
              />
            </template>
            <template #title>
              Nikt nie pracuje zdalnie
            </template>
          </EmptyState>
        </TabPanel>
      </TabPanels>
    </TabGroup>
  </section>
</template>

<script setup>
import EmptyState from '@/Shared/Feedbacks/EmptyState.vue'
import HomeCityIcon from 'vue-material-design-icons/HomeCity.vue'
import { TabGroup, TabList, Tab, TabPanels, TabPanel } from '@headlessui/vue'

defineProps({
  remoteDays: Object,
  upcomingRemoteDays: Object,
})
</script>
