<script setup>
import { Dialog, DialogOverlay, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'
import TextArea from '@/Shared/Forms/TextArea.vue'
import { ref, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { debounce } from 'lodash'
import UserProfileLink from '@/Shared/UserProfileLink.vue'
import { DateTime } from 'luxon'
import { useGlobalProps } from '@/Composables/useGlobalProps'
import AppLayout from '@/Shared/Layout/AppLayout.vue'

const props = defineProps({
  current: String,
  users: Object,
  benefits: Object,
  benefitsReports: Object,
  assignedBenefits: Object,
})

const { auth } = useGlobalProps()

const currentDate = DateTime.now()

const creatingBenefitsReport = ref(false)

const form = useForm({
  items: props.users.data.map((user) => {
    const item = props.assignedBenefits.data ? props.assignedBenefits.data.find((assignedBenefit) => assignedBenefit.user === user.id) : {
      benefits: [],
      comment: null,
    }

    return {
      user: user,
      comment: item?.comment,
      benefits: props.benefits.data.map((benefit) => {
        const assignedBenefit = item?.benefits.find((assignedBenefit) => assignedBenefit.id === benefit.id)

        return {
          id: benefit.id,
          employee: typeof assignedBenefit !== 'undefined' && assignedBenefit.employee ? assignedBenefit.employee / 100 : null,
          employer: typeof assignedBenefit !== 'undefined' && assignedBenefit.employer ? assignedBenefit.employer / 100 : null,
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
            employee: benefit.employee ? benefit.employee * 100 : null,
            employer: benefit.employer ? benefit.employer * 100 : null,
          })),
          comment: item.comment,
        }
      }),
    }))
    .put('/assigned-benefits')
}

function startCreatingBenefitsReport() {
  const name = currentDate.toLocaleString({ month: 'long', year: 'numeric' })

  formBenefitsReport.name = name.at(0).toUpperCase() + name.slice(1)
  creatingBenefitsReport.value = true
}

function submitCreateBenefitsReport() {
  formBenefitsReport.post('/benefits-reports')
}

function calculateSumOfBenefits(benefits) {
  let sum = 0

  for (const benefit of benefits) {
    if (benefit.employer) {
      sum += benefit.employer * 100
    }
  }

  return (new Intl.NumberFormat('pl-PL', { style: 'currency', currency: 'PLN' })).format(sum / 100)
}

function isBenefitHasCompanion(benefitId) {
  return props.benefits.data.find((benefit) => benefit.id === benefitId && benefit.companion === true)
}
</script>

<template>
  <AppLayout title="Aktualne benefity">
    <div class="bg-white shadow-md">
      <div class="flex justify-between items-center p-4 sm:px-6">
        <div>
          <h2 class="text-lg font-medium leading-6 text-gray-900">
            Aktualne benefity
          </h2>
        </div>
        <div v-if="auth.can.manageBenefits && benefits.data.length > 0">
          <button
            :class="[form.isDirty ? 'disabled:opacity-60' : 'hover:bg-blumilk-700']"
            :disabled="form.isDirty"
            class="inline-flex items-center py-3 px-4 text-sm font-medium leading-4 text-white bg-blumilk-600 rounded-md border border-transparent focus:outline-none focus:ring-2 focus:ring-blumilk-500 focus:ring-offset-2 shadow-sm"
            type="button"
            @click="startCreatingBenefitsReport"
          >
            Utwórz raport
          </button>
        </div>
      </div>
      <form @submit.prevent="submitAssignedBenefits">
        <div class="overflow-x-auto">
          <table class="w-full text-sm text-center border border-gray-300 divide-y divide-x divide-gray-300">
            <thead class="divide-y divide-gray-300">
              <tr class="divide-x divide-gray-300">
                <th
                  class="py-2 w-64 text-lg font-semibold text-gray-800 sticky left-0 bg-white outline outline-1 outline-offset-0 outline-gray-300"
                  rowspan="2"
                >
                  <div class="flex justify-center items-center capitalize">
                    {{ currentDate.toLocaleString({ month: 'long', year: 'numeric' }) }}
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
                  class="p-2 text-base font-semibold text-gray-900 even:bg-gray-100"
                  rowspan="2"
                >
                  Wykorzystane dofinansowanie
                </th>
                <th
                  class="p-2 text-base font-semibold text-gray-900 even:bg-gray-100"
                  rowspan="2"
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
                    class="text-sm p-1 font-normal bg-blumilk-50 text-blumilk-800 border border-gray-300"
                    style="min-width: 90px;"
                  >
                    Pracodawca
                  </th>
                  <th
                    class="text-sm p-1 font-normal bg-green-50 text-green-800 border border-gray-300"
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
                :class="[item.user.isActive ? '' : 'bg-gray-100', 'group hover:bg-blumilk-25 divide-x divide-gray-300']"
              >
                <th :class="[item.user.isActive ? 'bg-white' : 'bg-gray-100', 'group p-2 sticky left-0 outline outline-1 outline-offset-0 outline-gray-300 hover:bg-blumilk-25 group-hover:bg-blumilk-25']">
                  <div class="flex justify-start items-center">
                    <UserProfileLink
                      :user="item.user"
                      class="flex justify-start items-center"
                    >
                      <span class="inline-flex justify-center items-center size-8 rounded-full">
                        <img :src="item.user.avatar">
                      </span>
                      <div class="ml-3">
                        <div class="text-sm font-medium text-gray-900 truncate">
                          {{ item.user.name }}
                        </div>
                      </div>
                    </UserProfileLink>
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
                      min="0"
                      step="0.01"
                      :class="[item.user.isActive ? '' : 'bg-gray-100', 'size-full sm:text-sm appearance-none border-none text-right p-0 px-3 m-0 ring-inset hover:bg-blumilk-25 group-hover:bg-blumilk-25 focus:bg-blumilk-25  focus:ring-2 focus:ring-blumilk-300']"
                      title="Wprowadź kwotę."
                      type="number"
                    >
                  </td>
                  <td style="height: 40px">
                    <input
                      v-model.number="benefit.employee"
                      :name="`${benefit.id}-employee-${index}`"
                      type="number"
                      step="0.01"
                      :class="[item.user.isActive ? '' : 'bg-gray-100', 'size-full sm:text-sm appearance-none border-none text-right p-0 px-3 m-0 ring-inset hover:bg-blumilk-25 group-hover:bg-blumilk-25 focus:bg-blumilk-25  focus:ring-2 focus:ring-blumilk-300']"
                      title="Wprowadź kwotę."
                      min="0"
                    >
                  </td>
                </template>
                <td>
                  <div class="w-full sm:text-sm focus:ring-white m-0 text-right p-3">
                    {{ calculateSumOfBenefits(item.benefits) }}
                  </div>
                </td>
                <td
                  class="px-0.5 ring-inset hover:bg-blumilk-25 focus-within:bg-blumilk-25 ring-blumilk-300 focus-within:ring-2"
                >
                  <TextArea
                    v-model="item.comment"
                    :resize="true"
                    :class="[item.user.isActive ? '' : 'bg-gray-100', 'size-full sm:text-sm border-none appearance-none mt-1 focus:ring-0 focus:bg-blumilk-25 group-hover:bg-blumilk-25 resize-y']"
                    style="min-height: 40px"
                  />
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="flex justify-end py-3 px-4 sm:px-6">
          <button
            :class="[form.processing || !form.isDirty ? 'disabled:opacity-60' : 'hover:bg-blumilk-700']"
            :disabled="form.processing || !form.isDirty"
            class="inline-flex justify-center py-2 px-4 text-sm font-medium text-white bg-blumilk-600 rounded-md border border-transparent focus:outline-none focus:ring-2 focus:ring-blumilk-500 focus:ring-offset-2 shadow-sm"
            type="submit"
          >
            Zapisz
          </button>
        </div>
      </form>
    </div>
    <TransitionRoot
      :show="creatingBenefitsReport"
      as="template"
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
            <DialogOverlay class="fixed inset-0 bg-gray-500/75 transition-opacity" />
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
              class="inline-block relative px-4 pt-5 pb-4 text-left align-bottom bg-white rounded-lg shadow-xl transition-all sm:p-6 sm:my-8 sm:w-full sm:max-w-sm sm:align-middle"
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
                      class="block text-sm font-medium text-gray-700 sm:mt-px"
                      for="name"
                    >
                      Nazwa
                    </label>
                    <div class="mt-2">
                      <input
                        id="name"
                        v-model="formBenefitsReport.name"
                        :class="{ 'border-red-300 text-red-900 focus:outline-none focus:ring-red-500 focus:border-red-500': formBenefitsReport.errors.name, 'focus:ring-blumilk-500 focus:border-blumilk-500 sm:text-sm border-gray-300': !formBenefitsReport.errors.name }"
                        class="block w-full max-w-lg rounded-md shadow-sm sm:text-sm"
                        type="text"
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
                    class="py-2 px-4 text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blumilk-500 focus:ring-offset-2 shadow-sm"
                    type="button"
                    @click="creatingBenefitsReport = false"
                  >
                    Anuluj
                  </button>
                  <button
                    :class="[formBenefitsReport.processing ? 'disabled:opacity-60' : 'hover:bg-blumilk-700']"
                    :disabled="formBenefitsReport.processing"
                    class="inline-flex justify-center py-2 px-4 text-base font-medium text-white bg-blumilk-600 rounded-md border border-transparent focus:outline-none focus:ring-2 focus:ring-blumilk-500 focus:ring-offset-2 shadow-sm sm:text-sm"
                    type="submit"
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
  </AppLayout>
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
