<script setup>
import { useMonthInfo } from '@/Composables/monthInfo.js'
import { Dialog, DialogOverlay, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'
import { Listbox, ListboxButton, ListboxOption, ListboxOptions } from '@headlessui/vue'
import { XCircleIcon, ChevronUpDownIcon, CheckIcon } from '@heroicons/vue/24/solid'
import TextArea from '@/Shared/Forms/TextArea.vue'
import { computed, ref, watch } from 'vue'
import { useForm } from '@inertiajs/inertia-vue3'
import { debounce } from 'lodash'

const props = defineProps({
  current: String,
  users: Object,
  benefits: Object,
  benefitsReports: Object,
  assignedBenefits: Object,
  years: Object,
  auth: Object,
})

const selectedItem = ref('Aktualne benefity')
const creatingBenefitsReport = ref(false)
const { findMonth } = useMonthInfo()
const currentMonth = computed(() => findMonth(props.current))

const form = useForm({
  items: props.users.data.map((user) => {
    const item = props.assignedBenefits.data ? props.assignedBenefits.data.find((assignedBenefit) => assignedBenefit.user === user.id) : { benefits: [], comment: null }

    return {
      user: user,
      comment: item?.comment,
      benefits: props.benefits.data.map((benefit) => {
        const assignedBenefit = item?.benefits.find((assignedBenefit) => assignedBenefit.id === benefit.id)

        return {
          id: benefit.id,
          employee: typeof assignedBenefit !== 'undefined' && assignedBenefit.employee ?  assignedBenefit.employee/100 : null,
          employer: typeof assignedBenefit !== 'undefined' && assignedBenefit.employer ?  assignedBenefit.employer/100 : null,
        }
      }),
    }
  }),
})
const formBenefitsReport = useForm({
  name: '',
})

watch(() => form.items, debounce(() => {
  submitAssignedBenefits()
}, 1000), { deep: true })

function submitAssignedBenefits() {
  form
    .transform(data => ({
      data: data.items.map((item) => {
        return {
          user: item.user.id,
          benefits: item.benefits.map((benefit) => ({
            id: benefit.id,
            employee: benefit.employee ? benefit.employee*100 : null,
            employer: benefit.employer ? benefit.employer*100 : null,
          })),
          comment: item.comment,
        }
      }),
    }))
    .put('/assigned-benefits')
}
function startCreatingBenefitsReport() {
  formBenefitsReport.name = `${currentMonth.value.name} ${props.years.selected.year}`
  creatingBenefitsReport.value = true
}
function submitCreateBenefitsReport() {
  formBenefitsReport.post('/benefits-report')
}
function calculateSumOfBenefits(benefits) {
  let sum = 0

  for(const benefit of benefits){
    if(benefit.employer){
      sum += benefit.employer*100
    }
  }

  return (new Intl.NumberFormat('pl-PL', { style: 'currency', currency: 'PLN' })).format(sum / 100)
}
function isBenefitHasCompanion(benefitId) {
  return props.benefits.data.find((benefit) => benefit.id === benefitId && benefit.companion === true)
}
</script>

<template>
  <InertiaHead title="Aktualne benefity" />
  <div class="bg-white shadow-md">
    <div class="flex justify-between items-center p-4 sm:px-6">
      <div>
        <h2 class="text-lg font-medium leading-6 text-gray-900">
          Aktualne benefity
        </h2>
      </div>
      <div v-if="auth.can.manageBenefits && benefits.data.length > 0">
        <button
          type="button"
          class="inline-flex items-center py-3 px-4 text-sm font-medium leading-4 text-white bg-blumilk-600 rounded-md border border-transparent focus:outline-none focus:ring-2 focus:ring-blumilk-500 focus:ring-offset-2 shadow-sm"
          :class="[form.isDirty ? 'disabled:opacity-60' : 'hover:bg-blumilk-700']"
          :disabled="form.isDirty"
          @click="startCreatingBenefitsReport"
        >
          Utwórz raport
        </button>
      </div>
    </div>
    <div class="flex-1 grid grid-cols-1 p-4 md:grid-cols-3 gap-4 border-t border-gray-200">
      <Listbox
        v-model="selectedItem"
        as="div"
      >
        <div class="relative">
          <ListboxButton
            class="relative py-2 pr-10 pl-3 w-full max-w-lg sm:text-sm text-left bg-white rounded-md border border-gray-300 focus:border-blumilk-500 focus:outline-none focus:ring-1 focus:ring-blumilk-500 shadow-sm cursor-default"
          >
            <span class="block truncate">
              {{ selectedItem }}
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
                v-slot="{ active, selected }"
                as="template"
                value="Aktualne benefity"
              >
                <InertiaLink
                  as="button"
                  method="get"
                  href="/assigned-benefits"
                  class="hover:bg-gray-100 cursor-default truncate select-none relative py-2 pl-3 pr-9 w-full text-left"
                  :class="[active ? 'bg-gray-100' : 'text-gray-900']"
                >
                  {{ selectedItem }}
                  <span
                    v-if="selected"
                    class="text-blumilk-600 absolute inset-y-0 right-0 flex items-center pr-4"
                  >
                    <CheckIcon class="w-5 h-5" />
                  </span>
                </InertiaLink>
              </ListboxOption>
              <ListboxOption
                v-for="benefitsReport in benefitsReports"
                :key="benefitsReport.name"
                as="template"
                :value="benefitsReport.name"
              >
                <InertiaLink
                  as="button"
                  method="get"
                  :href="`/benefits-report/${benefitsReport.id}`"
                  class="hover:bg-gray-100 cursor-default truncate select-none relative py-2 pl-3 pr-9 w-full text-left"
                >
                  {{ benefitsReport.name }}
                </InertiaLink>
              </ListboxOption>
            </ListboxOptions>
          </transition>
        </div>
      </Listbox>
      <div
        v-if="Object.keys(form.errors).length !== 0"
        class="md:col-span-2 max-w-lg"
      >
        <div class="p-2 bg-red-50 rounded-md shadow-sm">
          <div class="flex">
            <div class="shrink-0">
              <XCircleIcon class="w-5 h-5 text-red-400" />
            </div>
            <div class="ml-3">
              <h3 class="text-sm font-medium text-red-800">
                Wystąpił błąd podczas przypisywania benefitów.
              </h3>
            </div>
          </div>
        </div>
      </div>
    </div>
    <form @submit.prevent="submitAssignedBenefits">
      <div class="overflow-x-auto">
        <table class="w-full text-sm text-center border border-gray-300 divide-y divide-x divide-gray-300">
          <thead class="divide-y divide-gray-300">
            <tr class="divide-x divide-gray-300">
              <th
                rowspan="2"
                class="py-2 w-64 text-lg font-semibold text-gray-800 sticky left-0 bg-white outline outline-1 outline-offset-0 outline-gray-300"
              >
                <div class="flex justify-center items-center">
                  {{ currentMonth.name }} {{ years.selected.year }}
                </div>
              </th>
              <th
                v-for="benefit in benefits.data"
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
              <th
                rowspan="2"
                class="p-2 text-base font-semibold text-gray-900 even:bg-gray-100"
                style="min-width: 150px;"
              >
                Notatki
              </th>
            </tr>
            <tr class="divide-x divide-gray-300">
              <template
                v-for="benefit in benefits.data"
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
                  style="min-width:90px;"
                >
                  Pracownik
                </th>
              </template>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-300">
            <tr
              v-for="(item, index) in form.items"
              :key="item.user.id"
              class="group hover:bg-blumilk-25 divide-x divide-gray-300"
            >
              <th class="group p-2 sticky left-0 outline outline-1 outline-offset-0 outline-gray-300 bg-white hover:bg-blumilk-25 group-hover:bg-blumilk-25">
                <div class="flex justify-start items-center">
                  <span class="inline-flex justify-center items-center w-8 h-8 rounded-full">
                    <img :src="item.user.avatar">
                  </span>
                  <div class="ml-3">
                    <div class="text-sm font-medium text-gray-900 truncate">
                      {{ item.user.name }}
                    </div>
                  </div>
                </div>
              </th>
              <template
                v-for="(benefit) in item.benefits"
                :key="benefit.id"
              >
                <td
                  v-if="!isBenefitHasCompanion(benefit.id)"
                  style="height: 40px"
                >
                  <input
                    v-model.number="benefit.employer"
                    :name="`${benefit.id}-employer-${index}`"
                    type="number"
                    step="0.01"
                    class="w-full h-full sm:text-sm appearance-none border-none text-right p-0 px-3 m-0 ring-inset hover:bg-blumilk-25 group-hover:bg-blumilk-25  focus:bg-blumilk-25 focus:ring-2 focus:ring-blumilk-300"
                    title="Wprowadź kwotę."
                    min="0"
                    dusk="grid-employer"
                  >
                </td>
                <td style="height: 40px">
                  <input
                    v-model.number="benefit.employee"
                    :name="`${benefit.id}-employee-${index}`"
                    type="number"
                    step="0.01"
                    class="w-full h-full sm:text-sm appearance-none border-none text-right p-0 px-3 m-0 ring-inset hover:bg-blumilk-25 group-hover:bg-blumilk-25 focus:bg-blumilk-25 focus:ring-2 focus:ring-blumilk-300"
                    title="Wprowadź kwotę."
                    min="0"
                    dusk="grid-employee"
                  >
                </td>
              </template>
              <td>
                <div
                  class="w-full sm:text-sm focus:ring-white m-0 text-right p-3"
                  dusk="grid-sum"
                >
                  {{ calculateSumOfBenefits(item.benefits) }}
                </div>
              </td>
              <td
                class="px-0.5 ring-inset hover:bg-blumilk-25 focus-within:bg-blumilk-25 ring-blumilk-300 focus-within:ring-2"
              >
                <TextArea
                  v-model="item.comment"
                  :resize="true"
                  class="w-full sm:text-sm border-none appearance-none mt-1 focus:ring-0 focus:bg-blumilk-25 group-hover:bg-blumilk-25 resize-y h-full"
                  style="min-height: 40px"
                />
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="flex justify-end py-3 px-4 sm:px-6">
        <button
          type="submit"
          class="inline-flex justify-center py-2 px-4 text-sm font-medium text-white bg-blumilk-600 rounded-md border border-transparent focus:outline-none focus:ring-2 focus:ring-blumilk-500 focus:ring-offset-2 shadow-sm"
          :class="[form.processing || !form.isDirty ? 'disabled:opacity-60' : 'hover:bg-blumilk-700']"
          :disabled="form.processing || !form.isDirty"
        >
          Zapisz
        </button>
      </div>
    </form>
  </div>
  <TransitionRoot
    as="template"
    :show="creatingBenefitsReport"
  >
    <Dialog
      is="div"
      class="overflow-y-auto fixed inset-0 z-10"
      @close="creatingBenefitsReport = false"
    >
      <div class="flex justify-center items-end px-4 pt-4 pb-20 min-h-screen text-center sm:block sm:p-0">
        <TransitionChild
          as="template"
          enter="ease-out duration-300"
          enter-from="opacity-0"
          enter-to="opacity-100"
          leave="ease-in duration-200"
          leave-from="opacity-100"
          leave-to="opacity-0"
        >
          <DialogOverlay class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" />
        </TransitionChild>

        <span class="hidden sm:inline-block sm:h-screen sm:align-middle">&#8203;</span>
        <TransitionChild
          as="template"
          enter="ease-out duration-300"
          enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
          enter-to="opacity-100 translate-y-0 sm:scale-100"
          leave="ease-in duration-200"
          leave-from="opacity-100 translate-y-0 sm:scale-100"
          leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        >
          <form
            class="inline-block relative px-4 pt-5 pb-4 text-left align-bottom bg-white rounded-lg shadow-xl transition-all transform sm:p-6 sm:my-8 sm:w-full sm:max-w-sm sm:align-middle"
            @submit.prevent="submitCreateBenefitsReport"
          >
            <div>
              <div>
                <DialogTitle
                  as="h3"
                  class="text-lg font-medium leading-6 text-center text-gray-900 font-sembiold"
                >
                  Utwórz raport
                </DialogTitle>
                <div class="mt-5">
                  <label
                    for="name"
                    class="block text-sm font-medium text-gray-700 sm:mt-px"
                  >
                    Nazwa
                  </label>
                  <div class="mt-2">
                    <input
                      id="name"
                      v-model="formBenefitsReport.name"
                      type="text"
                      class="block w-full max-w-lg rounded-md shadow-sm sm:text-sm"
                      :class="{ 'border-red-300 text-red-900 focus:outline-none focus:ring-red-500 focus:border-red-500': formBenefitsReport.errors.name, 'focus:ring-blumilk-500 focus:border-blumilk-500 sm:text-sm border-gray-300': !formBenefitsReport.errors.name }"
                    >
                    <p
                      v-if="formBenefitsReport.errors.name"
                      class="mt-2 text-sm text-red-600"
                    >
                      {{ formBenefitsReport.errors.name }}
                    </p>
                  </div>
                </div>
              </div>
            </div>
            <div class="mt-5 sm:mt-6">
              <div class="flex justify-end space-x-3">
                <button
                  type="button"
                  class="py-2 px-4 text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blumilk-500 focus:ring-offset-2 shadow-sm"
                  @click="creatingBenefitsReport = false"
                >
                  Anuluj
                </button>
                <button
                  type="submit"
                  class="inline-flex justify-center py-2 px-4 text-base font-medium text-white bg-blumilk-600 rounded-md border border-transparent focus:outline-none focus:ring-2 focus:ring-blumilk-500 focus:ring-offset-2 shadow-sm sm:text-sm"
                  :class="[formBenefitsReport.processing ? 'disabled:opacity-60' : 'hover:bg-blumilk-700']"
                  :disabled="formBenefitsReport.processing"
                >
                  Utwórz
                </button>
              </div>
            </div>
          </form>
        </TransitionChild>
      </div>
    </Dialog>
  </TransitionRoot>
</template>

<style>
input.appearance-none::-webkit-outer-spin-button,
input.appearance-none::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

input[type=number].appearance-none {
  -moz-appearance: textfield;
}
</style>
