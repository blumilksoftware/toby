<script setup>
import VacationStats from '@/Shared/Widgets/VacationStats.vue'
import UserVacationRequests from '@/Shared/Widgets/UserVacationRequests.vue'
import BenefitList from '@/Shared/Widgets/BenefitList.vue'
import { ComputerDesktopIcon } from '@heroicons/vue/24/outline'
import EmptyState from '@/Shared/Feedbacks/EmptyState.vue'
import UserOvertimeRequests from '@/Shared/Widgets/UserOvertimeRequests.vue'

defineProps({
  user: Object,
  current: Object,
  upcoming: Object,
  vacationRequests: Object,
  overtimeRequests: Object,
  stats: Object,
  calendar: Object,
  years: Object,
  benefits: Object,
  upcomingBirthday: Object,
  seniority: String,
  equipmentItems: Object,
})
</script>

<template>
  <InertiaHead title="Profil użytkownika" />
  <div class="grid grid-cols-1 gap-4 items-start xl:grid-cols-3 xl:gap-8">
    <div class="grid grid-cols-1 gap-4 xl:col-span-2">
      <section>
        <div class=" overflow-hidden bg-white shadow">
          <div class="p-6 bg-white">
            <div class="sm:flex sm:justify-between sm:items-center">
              <div class="sm:flex sm:space-x-5 sm:items-center">
                <div class="shrink-0">
                  <img
                    :src="user.avatar"
                    class="mx-auto w-20 h-20 rounded-full"
                  >
                </div>
                <div class="mt-4 text-center sm:pt-1 sm:mt-0 sm:text-left">
                  <p class="text-xl font-bold text-gray-900 sm:text-2xl">
                    {{ user.name }}
                  </p>
                  <p class="text-sm font-medium text-gray-600">
                    {{ user.role }}
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
      <VacationStats :stats="stats" />
      <UserVacationRequests
        :label="'Wnioski'"
        :profile-page="true"
        :requests="vacationRequests.data"
      />
      <UserOvertimeRequests :requests="overtimeRequests.data" />
    </div>
    <div class="grid grid-cols-1 gap-4">
      <section class="bg-white shadow-md">
        <div class="w-100 p-4 sm:px-6 border-b border-gray-200 flex items-center">
          <h2 class="text-lg font-medium leading-6 text-gray-900 w-full">
            Informacje
          </h2>
        </div>
        <div class="px-4 border-t border-gray-200 sm:px-6">
          <ul class="divide-y divide-gray-200">
            <li class="flex py-4">
              <div>
                <p class="text-sm text-gray-500">
                  Urodziny: <span
                    v-if="upcomingBirthday"
                    class="font-bold"
                  >{{ upcomingBirthday.displayDate }} - {{ upcomingBirthday.relativeDate }}</span>
                  <span v-else>-</span>
                </p>
              </div>
            </li>
            <li class="flex py-4">
              <div>
                <p class="text-sm text-gray-500">
                  Staż pracy: <span class="font-bold">{{ seniority ?? "-" }}</span>
                </p>
              </div>
            </li>
          </ul>
        </div>
      </section>
      <BenefitList
        :benefits="benefits.data"
        :label="'Benefity'"
      />
      <section class="bg-white shadow-md">
        <div class="w-100 p-4 sm:px-6 border-b border-gray-200 flex items-center">
          <h2 class="text-lg font-medium leading-6 text-gray-900 w-full">
            Sprzęt
          </h2>
        </div>
        <div class="px-4 border-t border-gray-200 sm:px-6">
          <ul
            v-if="equipmentItems.data.length"
            class="divide-y divide-gray-200"
          >
            <li
              v-for="item in equipmentItems.data"
              :key="item.id"
              class="flex py-4"
            >
              <div class="ml-3">
                <p class="text-sm font-medium text-gray-900">
                  {{ item.name }}
                </p>
                <p class="text-sm text-gray-500">
                  {{ item.idNumber }}
                </p>
                <template v-if="item.labels && item.labels.length !== 0">
                  <span
                    v-for="(label, index) in item.labels"
                    :key="index"
                    class="inline-flex items-center py-1.5 px-3 mr-1 my-1 rounded-lg text-sm font-medium bg-blumilk-500 text-white"
                  >
                    {{ label }}
                  </span>
                </template>
              </div>
            </li>
          </ul>
          <EmptyState
            v-else
            :show-description="false"
          >
            <template #head>
              <ComputerDesktopIcon class="h-12 w-12 mx-auto" />
            </template>
            <template #title>
              Brak sprzętu
            </template>
          </EmptyState>
          <div
            v-if="equipmentItems.data.length"
            class="mb-6"
          >
            <InertiaLink
              :href="`/equipment-items?assignee=${user.id}`"
              class="flex justify-center items-center py-2 px-4 w-full text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blumilk-500 shadow-sm"
            >
              Zobacz wszystkie
            </InertiaLink>
          </div>
        </div>
      </section>
    </div>
  </div>
</template>
