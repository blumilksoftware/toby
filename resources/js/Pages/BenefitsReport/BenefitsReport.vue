<script setup>
import { Listbox, ListboxButton, ListboxOption, ListboxOptions } from '@headlessui/vue'
import { ChevronUpDownIcon, CheckIcon } from '@heroicons/vue/24/solid'
import { computed, ref } from 'vue'

const props = defineProps({
  benefitsReport: Object,
  benefitsReports: Object,
  auth: Object,
})

const benefitsReportData = props.benefitsReport.data.map((item) => {
  const user = props.benefitsReport.users.find((user) => item.user === user.id)

  return {
    user: user,
    benefits: item.benefits,
  }
})

const selectedBenefitsReport = ref(props.benefitsReport.name)
const selectedUsers = ref([])
const indeterminate = computed(() => selectedUsers.value.length > 0 && selectedUsers.value.length < benefitsReportData.length)

function calculateSumOfBenefits(benefits) {
  let sum = 0

  for(const benefit of benefits){
    if(benefit.employer){
      sum += benefit.employer
    }
  }

  return (new Intl.NumberFormat('pl-PL', { style: 'currency', currency: 'PLN' })).format(sum / 100)
}
function isBenefitHasCompanion(benefitId) {
  return props.benefitsReport.benefits.find((benefit) => benefit.id === benefitId && benefit.companion === true)
}
function generateUrl(){
  const params = new URLSearchParams()

  selectedUsers.value.forEach((id) =>  params.append('users[]', id))

  return params
}
</script>

<template>
  <InertiaHead title="Raport benefitów" />
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
          :href="`/benefits-report/${props.benefitsReport.id}/download?${generateUrl()}`"
        >
          <button
            class="inline-flex items-center py-3 px-4 text-sm font-medium leading-4 text-white bg-blumilk-600 rounded-md border border-transparent focus:outline-none focus:ring-2 focus:ring-blumilk-500 focus:ring-offset-2 shadow-sm"
            :class="[selectedUsers.length === 0 ? 'disabled:opacity-60' : 'hover:bg-blumilk-700']"
          >
            Pobierz raport
          </button>
        </a>
        <button
          v-else
          class="inline-flex items-center py-3 px-4 text-sm font-medium leading-4 text-white bg-blumilk-600 rounded-md border border-transparent focus:outline-none focus:ring-2 focus:ring-blumilk-500 focus:ring-offset-2 shadow-sm"
          :class="[selectedUsers.length === 0 ? 'disabled:opacity-60' : 'hover:bg-blumilk-700']"
          :disabled="selectedUsers.length === 0"
        >
          Pobierz raport
        </button>
      </div>
    </div>
    <div class="flex-1 grid grid-cols-1 p-4 md:grid-cols-3 gap-4 border-t border-gray-200">
      <Listbox
        v-model="selectedBenefitsReport"
        as="div"
      >
        <div class="relative">
          <ListboxButton
            class="relative py-2 pr-10 pl-3 w-full max-w-lg text-left bg-white rounded-md border border-gray-300 focus:border-blumilk-500 focus:outline-none focus:ring-1 focus:ring-blumilk-500 shadow-sm cursor-default sm:text-sm"
          >
            <span class="block truncate w-48">
              {{ benefitsReport.name }}
            </span>
            <span class="flex absolute inset-y-0 right-0 items-center pr-2 pointer-events-none">
              <ChevronUpDownIcon class="w-5 h-5 text-gray-400" />
            </span>
          </ListboxButton>
          <transition
            leave-active-class="transition ease-in duration-100"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
          >
            <ListboxOptions
              class="overflow-auto absolute z-10 py-1 mt-1 w-full max-w-lg max-h-60 text-base bg-white rounded-md focus:outline-none ring-1 ring-black ring-opacity-5 shadow-lg sm:text-sm"
            >
              <ListboxOption
                v-for="currentBenefitsReport in benefitsReports"
                :key="currentBenefitsReport.name"
                v-slot="{ active, selected }"
                as="template"
                :value="currentBenefitsReport.name"
              >
                <InertiaLink
                  as="button"
                  method="get"
                  :href="`/benefits-report/${currentBenefitsReport.id}`"
                  class="hover:bg-gray-100 cursor-default truncate select-none relative py-2 pl-3 pr-9 w-full text-left"
                  :class="[active ? 'bg-gray-100' : 'text-gray-900']"
                >
                  {{ currentBenefitsReport.name }}
                  <span
                    v-if="selected"
                    :class="['text-blumilk-600 absolute inset-y-0 right-0 flex items-center pr-4']"
                  >
                    <CheckIcon class="w-5 h-5" />
                  </span>
                </InertiaLink>
              </ListboxOption>
            </ListboxOptions>
          </transition>
        </div>
      </Listbox>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-sm text-center border border-gray-300 divide-y divide-x divide-gray-300">
        <thead class="divide-y divide-gray-300">
          <tr class="divide-x divide-gray-300">
            <th
              scope="col"
              rowspan="2"
              class="relative w-16 px-8 space-x-4 sticky left-0 bg-white outline outline-1 outline-offset-0 outline-gray-300"
            >
              <input
                type="checkbox"
                class="absolute left-6 top-1/2 -mt-2 h-4 w-4 rounded border-gray-300 text-blumilk-600 focus:ring-blumilk-500"
                :disabled="benefitsReportData.length === 0"
                :checked="indeterminate || selectedUsers.length === benefitsReportData.length && benefitsReportData.length > 0"
                :indeterminate="indeterminate"
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
              rowspan="2"
              class="p-2 text-base font-semibold text-gray-900 even:bg-gray-100"
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
            :class="[ selectedUsers.find((selectedUser) => selectedUser === item.user.id) && 'bg-blumilk-25']"
            class="group bg-white hover:bg-blumilk-25 group-hover:bg-blumilk-25 divide-x divide-gray-300"
          >
            <td
              :class="[ selectedUsers.find((selectedUser) => selectedUser === item.user.id) && 'bg-blumilk-25']"
              class="relative w-12 px-6 sm:w-16 sm:px-8 space-x-4 sticky left-0 outline outline-1 outline-offset-0 outline-gray-300 group bg-white hover:bg-blumilk-25 group-hover:bg-blumilk-25"
            >
              <div>
                <div
                  v-if="selectedUsers.find((selectedUser) => selectedUser === item.user.id)"
                  class="absolute inset-y-0 left-0 w-0.5 bg-blumilk-600"
                />
                <input
                  v-model="selectedUsers"
                  type="checkbox"
                  class="absolute left-6 top-1/2 -mt-2 h-4 w-4 rounded border-gray-300 text-blumilk-600 focus:ring-blumilk-500"
                  :value="item.user.id"
                >
              </div>
              <div class="flex justify-start items-center pl-4">
                <span class="inline-flex justify-center items-center w-8 h-8 rounded-full">
                  <img :src="item.user.avatar">
                </span>
                <div class="ml-3">
                  <div class="text-sm font-medium text-gray-900 truncate">
                    {{ item.user.name }}
                  </div>
                </div>
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
                <span v-if="benefit.employer">{{ new Intl.NumberFormat('pl-PL').format(benefit.employer/100) }}</span>
              </td>
              <td class="text-right px-3">
                <span v-if="benefit.employee">{{ new Intl.NumberFormat('pl-PL').format(benefit.employee/100) }}</span>
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
