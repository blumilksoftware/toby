<template>
  <section class="bg-white shadow-md">
    <div class="p-4 sm:px-6">
      <TabGroup>
        <TabList class="w-100">
          <Tab class="w-1/2">
            <button
                :class="[selected ? 'bg-blue-500 text-white' : 'bg-white text-black']"
            >
              <h2 class="text-lg font-medium leading-6 text-gray-900">Dzisiejsza praca zdalna</h2>
            </button>
          </Tab>
          <Tab class="w-1/2">
            <button
                :class="[selected ? 'bg-blue-500 text-white' : 'bg-white text-black']"
            >
              <h2 class="text-lg font-medium leading-6 text-gray-900">Nadchodząca praca zdalna</h2>
            </button>
          </Tab>
        </TabList>
        <TabPanels>
          <TabPanel>
            <ul class="divide-y divide-gray-200">
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
          </TabPanel>
          <TabPanel>
            <ul class="divide-y divide-gray-200">
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
          </TabPanel>
        </TabPanels>
      </TabGroup>
    </div>
  </section>
</template>

<script setup>
import {TabGroup, TabList, Tab, TabPanels, TabPanel} from '@headlessui/vue'

defineProps({
  remoteDays: Object,
  upcomingRemoteDays: Object,
})
</script>
