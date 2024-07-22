<script setup>
import { computed, ref } from 'vue'
import UserProfileLink from '@/Shared/UserProfileLink.vue'

const props = defineProps({
  benefitsReport: Object,
  auth: Object,
})

const benefitsReportData = props.benefitsReport.data.map((item) => {
  const user = props.benefitsReport.users.find((user) => item.user === user.id)

  return {
    user: user,
    benefits: item.benefits,
  }
})

const selectedUsers = ref([])
const indeterminate = computed(() => selectedUsers.value.length > 0 && selectedUsers.value.length < benefitsReportData.length)

function calculateSumOfBenefits(benefits) {
  let sum = 0

  for (const benefit of benefits) {
    if (benefit.employer) {
      sum += benefit.employer
    }
  }

  return (new Intl.NumberFormat('pl-PL', { style: 'currency', currency: 'PLN' })).format(sum / 100)
}

function isBenefitHasCompanion(benefitId) {
  return props.benefitsReport.benefits.find((benefit) => benefit.id === benefitId && benefit.companion === true)
}

function generateUrl() {
  const params = new URLSearchParams()

  selectedUsers.value.forEach((id) => params.append('users[]', id))

  return params
}
</script>

<template>
  <InertiaHead :title="`Raport benefitowy - ${benefitsReport.name}`" />
  <div class="bg-white shadow-md">
    <div class="flex justify-between items-center p-4 sm:px-6">
      <div>
        <h2 class="text-lg font-medium leading-6 text-gray-900">
          Benefity - {{ benefitsReport.name }}
        </h2>
      </div>
      <div v-if="auth.can.manageBenefits">
        <a
          v-if="selectedUsers.length !== 0"
          :href="`/benefits-reports/${props.benefitsReport.id}/download?${generateUrl()}`"
        >
          <button
            :class="[selectedUsers.length === 0 ? 'disabled:opacity-60' : 'hover:bg-blumilk-700']"
            class="inline-flex items-center py-3 px-4 text-sm font-medium leading-4 text-white bg-blumilk-600 rounded-md border border-transparent focus:outline-none focus:ring-2 focus:ring-blumilk-500 focus:ring-offset-2 shadow-sm"
          >
            Pobierz raport
          </button>
        </a>
        <button
          v-else
          :class="[selectedUsers.length === 0 ? 'disabled:opacity-60' : 'hover:bg-blumilk-700']"
          :disabled="selectedUsers.length === 0"
          class="inline-flex items-center py-3 px-4 text-sm font-medium leading-4 text-white bg-blumilk-600 rounded-md border border-transparent focus:outline-none focus:ring-2 focus:ring-blumilk-500 focus:ring-offset-2 shadow-sm"
        >
          Pobierz raport
        </button>
      </div>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-sm text-center border border-gray-300 divide-y divide-x divide-gray-300">
        <thead class="divide-y divide-gray-300">
          <tr class="divide-x divide-gray-300">
            <th
              class="relative w-16 px-8 space-x-4 sticky left-0 bg-white outline outline-1 outline-offset-0 outline-gray-300"
              rowspan="2"
              scope="col"
            >
              <input
                :checked="indeterminate || selectedUsers.length === benefitsReportData.length && benefitsReportData.length > 0"
                :disabled="benefitsReportData.length === 0"
                :indeterminate="indeterminate"
                class="absolute left-6 top-1/2 -mt-2 h-4 w-4 rounded border-gray-300 text-blumilk-600 focus:ring-blumilk-500"
                type="checkbox"
                @change="selectedUsers = $event.target.checked ? benefitsReportData.map((item) => ( item.user.id )) : []"
              >
              <div class="flex justify-start items-center pl-4">
                {{ benefitsReport.name }}
              </div>
            </th>
            <th
              v-for="benefit in benefitsReport.benefits"
              :key="benefit.id"
              :colspan="[!benefit.companion ? 2 : 0]"
              class="p-2 text-base font-semibold text-gray-900 even:bg-gray-100"
              style="min-width: 150px"
            >
              <div>
                {{ benefit.name }}
              </div>
            </th>
            <th
              class="p-2 text-base font-semibold text-gray-900 even:bg-gray-100"
              rowspan="2"
            >
              Wykorzystane dofinansowanie
            </th>
          </tr>
          <tr class="divide-x divide-gray-300">
            <template
              v-for="benefit in benefitsReport.benefits"
              :key="benefit.id"
            >
              <th
                v-if="!benefit.companion"
                class="text-sm p-1 font-normal text-gray-900 bg-blumilk-50 text-blumilk-800 border border-gray-300"
                style="min-width: 90px;"
              >
                Pracodawca
              </th>
              <th
                class="text-sm p-1 font-normal text-gray-900 bg-green-50 text-green-800 border border-gray-300"
                style="min-width: 90px;"
              >
                Pracownik
              </th>
            </template>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-300">
          <tr
            v-for="item in benefitsReportData"
            :key="item.user.id"
            :class="[ selectedUsers.find((selectedUser) => selectedUser === item.user.id) && 'bg-blumilk-25', [!item.user.isActive ? 'bg-gray-100' : 'bg-white']]"
            class="group hover:bg-blumilk-25 group-hover:bg-blumilk-25 divide-x divide-gray-300"
          >
            <td
              :class="[ selectedUsers.find((selectedUser) => selectedUser === item.user.id) && 'bg-blumilk-25', [!item.user.isActive ? 'bg-gray-100' : 'bg-white']]"
              class="relative w-12 px-6 sm:w-16 sm:px-8 space-x-4 sticky left-0 outline outline-1 outline-offset-0 outline-gray-300 group hover:bg-blumilk-25 group-hover:bg-blumilk-25"
            >
              <div>
                <div
                  v-if="selectedUsers.find((selectedUser) => selectedUser === item.user.id)"
                  class="absolute inset-y-0 left-0 w-0.5 bg-blumilk-600"
                />
                <input
                  v-model="selectedUsers"
                  :value="item.user.id"
                  class="absolute left-6 top-1/2 -mt-2 h-4 w-4 rounded border-gray-300 text-blumilk-600 focus:ring-blumilk-500"
                  type="checkbox"
                >
              </div>
              <div class="flex justify-start items-center pl-4">
                <UserProfileLink
                  :user="item.user"
                  class="flex justify-start items-center"
                >
                  <span class="inline-flex justify-center items-center w-8 h-8 rounded-full">
                    <img :src="item.user.avatar">
                  </span>
                  <div class="ml-3">
                    <div class="text-sm font-medium text-gray-900 truncate">
                      {{ item.user.name }}
                    </div>
                  </div>
                </UserProfileLink>
              </div>
            </td>
            <template
              v-for="(benefit) in item.benefits"
              :key="benefit.id"
            >
              <td
                v-if="!isBenefitHasCompanion(benefit.id)"
                class="text-right px-3"
              >
                <span v-if="benefit.employer">{{ new Intl.NumberFormat('pl-PL').format(benefit.employer / 100) }}</span>
              </td>
              <td class="text-right px-3">
                <span v-if="benefit.employee">{{ new Intl.NumberFormat('pl-PL').format(benefit.employee / 100) }}</span>
              </td>
            </template>
            <td>
              <div class="w-full sm:text-sm focus:ring-white m-0 text-right p-3">
                {{ calculateSumOfBenefits(item.benefits) }}
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
