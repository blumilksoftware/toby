<script setup>
import { useForm } from '@inertiajs/inertia-vue3'
import FlatPickr from 'vue-flatpickr-component'
import { Listbox, ListboxButton, ListboxLabel, ListboxOption, ListboxOptions, Switch } from '@headlessui/vue'
import { CheckIcon, ChevronUpDownIcon, XCircleIcon } from '@heroicons/vue/24/solid'
import { reactive, ref, watch } from 'vue'
import axios from 'axios'
import useCurrentYearPeriodInfo from '@/Composables/yearPeriodInfo.js'
import VacationChart from '@/Shared/VacationChart.vue'
import VacationType from '@/Shared/VacationType.vue'

const props = defineProps({
  auth: Object,
  users: Object,
  holidays: Object,
  can: Object,
  vacationUserId: [Number, null],
  vacationFromDate: [String, null],
})

const form = useForm({
  user: props.can.createOnBehalfOfEmployee
    ? props.users.data.find(user => user.id === (checkUserId(props.vacationUserId) ?? props.auth.user.id)) ?? props.users.data[0]
    : props.auth.user,
  from: props.vacationFromDate,
  to: props.vacationFromDate,
  vacationType: null,
  comment: null,
  flowSkipped: false,
})

let isDirty = ref(false)

watch(form, formData => {
  const { from, to } = formData.data()
  isDirty.value = formData.isDirty || from !== null || to !== null
}, { immediate: true, deep: true })

refreshEstimatedDays(form.from, form.to)

const estimatedDays = ref([])
const vacationTypes = ref([])

const stats = ref({
  used: 0,
  pending: 0,
  remaining: 0,
})

const { minDate, maxDate } = useCurrentYearPeriodInfo()

const weekends = date => (date.getDay() === 0 || date.getDay() === 6)

const fromInputConfig = reactive({
  minDate,
  maxDate,
  disable: [weekends],
})

const toInputConfig = reactive({
  minDate,
  maxDate,
  disable: [weekends],
})

watch(() => form.user, user => {
  resetForm()
  refreshAvailableTypes(user)
  refreshUnavailableDays(user)
  refreshVacationStats(user)
}, { immediate: true })

function createForm() {
  form
    .transform(data => ({
      ...data,
      type: data.vacationType.value,
      user: data.user.id,
    }))
    .post('/vacation/requests')
}

function onFromChange(selectedDates, dateStr) {
  if (form.to === null) {
    form.to = dateStr

    return
  }

  refreshEstimatedDays(form.from, form.to)
}

function onToChange(selectedDates, dateStr) {
  if (form.from === null) {
    form.from = dateStr

    return
  }
  refreshEstimatedDays(form.from, form.to)
}

function resetForm() {
  form.reset('to', 'from', 'comment', 'flowSkipped')
  form.clearErrors()
  estimatedDays.value = []
}

function checkUserId(userId) {
  return userId > 0 ? userId: null
}

async function refreshEstimatedDays(from, to) {
  if (from && to) {
    const res = await axios.post('/api/vacation/calculate-days', { from, to })

    estimatedDays.value = res.data
  }
}

async function refreshVacationStats(user) {
  const res = await axios.post('/api/vacation/calculate-stats', { user: user.id })

  stats.value = res.data
}

async function refreshUnavailableDays(user) {
  const res = await axios.post('/api/vacation/calculate-unavailable-days', { user: user.id })
  const unavailableDays = res.data

  fromInputConfig.disable = [
    weekends,
    ...unavailableDays,
  ]

  toInputConfig.disable = [
    weekends,
    ...unavailableDays,
  ]
}

async function refreshAvailableTypes(user) {
  const res = await axios.post('/api/vacation/get-available-vacation-types', { user: user.id })

  vacationTypes.value = res.data
  form.vacationType = vacationTypes.value[0]
  form.defaults('vacationType', vacationTypes.value[0])
}

</script>

<template>
  <InertiaHead title="Dodaj wniosek" />
  <div :class="[stats.limit > 0 ? ' grid grid-cols-1 gap-4 items-start xl:grid-cols-3 xl:gap-8' : 'mx-auto w-full max-w-7xl']">
    <div class="flex flex-col h-full bg-white shadow-md xl:col-span-2">
      <div class="p-4 sm:px-6">
        <h2 class="text-lg font-medium leading-6 text-gray-900">
          Dodaj wniosek
        </h2>
      </div>
      <form
        class="px-6 h-full border-t border-gray-200"
        @submit.prevent="createForm"
      >
        <div class="flex flex-col justify-around h-full">
          <div>
            <div
              v-if="form.errors.vacationRequest"
              class="p-4 mt-2 bg-red-50 rounded-md"
            >
              <div class="flex">
                <div class="shrink-0">
                  <XCircleIcon class="w-5 h-5 text-red-400" />
                </div>
                <div class="ml-3">
                  <h3 class="text-sm font-medium text-red-800">
                    Wniosek nie mógł zostać utworzony
                  </h3>
                  <div class="mt-2 text-sm text-red-700">
                    <span>{{ form.errors.vacationRequest }}</span>
                  </div>
                </div>
              </div>
            </div>
            <Listbox
              v-if="can.createOnBehalfOfEmployee"
              v-model="form.user"
              as="div"
              class="items-center py-4 sm:grid sm:grid-cols-3"
            >
              <ListboxLabel class="block text-sm font-medium text-gray-700">
                Osoba składająca wniosek
              </ListboxLabel>
              <div class="relative mt-1 sm:col-span-2 sm:mt-0">
                <ListboxButton
                  class="relative py-2 pr-10 pl-3 w-full max-w-lg text-left bg-white rounded-md border focus:outline-none focus:ring-1 shadow-sm cursor-default sm:text-sm"
                  :class="{ 'border-red-300 text-red-900 focus:ring-red-500 focus:border-red-500': form.errors.user, 'focus:ring-blumilk-500 focus:border-blumilk-500 sm:text-sm border-gray-300': !form.errors.user }"
                >
                  <span class="flex items-center">
                    <img
                      :src="form.user.avatar"
                      class="shrink-0 w-6 h-6 rounded-full"
                    >
                    <span class="block ml-3 truncate">{{ form.user.name }}</span>
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
                      v-for="user in users.data"
                      :key="user.id"
                      v-slot="{ active, selected }"
                      as="template"
                      :value="user"
                    >
                      <li :class="[active ? 'bg-gray-100' : 'text-gray-900', 'cursor-default select-none relative py-2 pl-3 pr-9']">
                        <div class="flex items-center">
                          <img
                            :src="user.avatar"
                            alt=""
                            class="shrink-0 w-6 h-6 rounded-full"
                          >
                          <span :class="[selected ? 'font-semibold' : 'font-normal', 'ml-3 block truncate']">
                            {{ user.name }}
                          </span>
                        </div>

                        <span
                          v-if="selected"
                          :class="['text-blumilk-600 absolute inset-y-0 right-0 flex items-center pr-4']"
                        >
                          <CheckIcon class="w-5 h-5" />
                        </span>
                      </li>
                    </ListboxOption>
                  </ListboxOptions>
                </transition>
                <p
                  v-if="form.errors.type"
                  class="mt-2 text-sm text-red-600"
                >
                  {{ form.errors.type }}
                </p>
              </div>
            </Listbox>
            <div
              v-else
              class="items-center py-4 sm:grid sm:grid-cols-3"
            >
              <label
                for="date_from"
                class="block text-sm font-medium text-gray-700 sm:mt-px"
              >
                Osoba składająca wniosek
              </label>
              <div class="mt-1 sm:col-span-2 sm:mt-0">
                <div class="flex justify-start items-center">
                  <span class="inline-flex justify-center items-center w-10 h-10 rounded-full">
                    <img
                      class="w-10 h-10 rounded-full"
                      :src="auth.user.avatar"
                    >
                  </span>
                  <div class="ml-3">
                    <div class="text-sm font-medium text-gray-900">
                      {{ auth.user.name }}
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <Listbox
              v-model="form.vacationType"
              as="div"
              class="items-center py-4 sm:grid sm:grid-cols-3"
            >
              <ListboxLabel class="block text-sm font-medium text-gray-700">
                Typ wniosku
              </ListboxLabel>
              <div class="relative mt-1 sm:col-span-2 sm:mt-0">
                <ListboxButton
                  class="relative py-2 pr-10 pl-3 w-full max-w-lg text-left bg-white rounded-md border focus:outline-none focus:ring-1 shadow-sm cursor-default sm:text-sm"
                  :class="{ 'border-red-300 text-red-900 focus:ring-red-500 focus:border-red-500': form.errors.type, 'focus:ring-blumilk-500 focus:border-blumilk-500 sm:text-sm border-gray-300': !form.errors.type }"
                  dusk="vacation-types-listbox-button"
                >
                  <template v-if="form.vacationType">
                    <span class="block truncate">
                      <VacationType :type="form.vacationType.value" />
                    </span>
                    <span class="flex absolute inset-y-0 right-0 items-center pr-2 pointer-events-none">
                      <ChevronUpDownIcon class="w-5 h-5 text-gray-400" />
                    </span>
                  </template>
                  <template v-else>
                    Ładowanie...
                  </template>
                </ListboxButton>
                <transition
                  leave-active-class="transition ease-in duration-100"
                  leave-from-class="opacity-100"
                  leave-to-class="opacity-0"
                >
                  <ListboxOptions class="overflow-auto absolute z-10 py-1 mt-1 w-full max-w-lg max-h-60 text-base bg-white rounded-md focus:outline-none ring-1 ring-black ring-opacity-5 shadow-lg sm:text-sm">
                    <ListboxOption
                      v-for="vacationType in vacationTypes"
                      :key="vacationType.value"
                      v-slot="{ active, selected }"
                      as="template"
                      :value="vacationType"
                      :dusk="vacationType.value"
                    >
                      <li 
                        :class="[active ? 'bg-gray-100' : 'text-gray-900', 'cursor-default select-none relative py-2 pl-3 pr-9']"
                      >
                        <span :class="[selected ? 'font-semibold' : 'font-normal', 'block truncate']">
                          <VacationType :type="vacationType.value" />
                        </span>

                        <span
                          v-if="selected"
                          :class="['text-blumilk-600 absolute inset-y-0 right-0 flex items-center pr-4']"
                        >
                          <CheckIcon class="w-5 h-5" />
                        </span>
                      </li>
                    </ListboxOption>
                  </ListboxOptions>
                </transition>
                <p
                  v-if="form.errors.type"
                  class="mt-2 text-sm text-red-600"
                >
                  {{ form.errors.type }}
                </p>
              </div>
            </Listbox>
            <div class="items-center py-4 sm:grid sm:grid-cols-3">
              <label
                for="date_from"
                class="block text-sm font-medium text-gray-700 sm:mt-px"
              >
                Data od
              </label>
              <div 
                class="mt-1 sm:col-span-2 sm:mt-0"
                dusk="date-from"
              >
                <FlatPickr
                  id="date_from"
                  v-model="form.from"
                  :config="fromInputConfig"
                  class="block w-full max-w-lg rounded-md shadow-sm sm:text-sm"
                  :class="{ 'border-red-300 text-red-900 focus:outline-none focus:ring-red-500 focus:border-red-500': form.errors.from, 'focus:ring-blumilk-500 focus:border-blumilk-500 sm:text-sm border-gray-300': !form.errors.from }"
                  @on-change="onFromChange"
                />
                <p
                  v-if="form.errors.from"
                  class="mt-2 text-sm text-red-600"
                >
                  {{ form.errors.from }}
                </p>
              </div>
            </div>
            <div class="items-center py-4 sm:grid sm:grid-cols-3">
              <label
                for="date_from"
                class="block text-sm font-medium text-gray-700 sm:mt-px"
              >
                Data do
              </label>
              <div 
                class="mt-1 sm:col-span-2 sm:mt-0"
                dusk="date-to"
              >
                <FlatPickr
                  id="date_to"
                  v-model="form.to"
                  :config="toInputConfig"
                  class="block w-full max-w-lg rounded-md shadow-sm sm:text-sm"
                  :class="{ 'border-red-300 text-red-900 focus:outline-none focus:ring-red-500 focus:border-red-500': form.errors.to, 'focus:ring-blumilk-500 focus:border-blumilk-500 sm:text-sm border-gray-300': !form.errors.to }"
                  @on-change="onToChange"
                />
                <p
                  v-if="form.errors.to"
                  class="mt-2 text-sm text-red-600"
                >
                  {{ form.errors.to }}
                </p>
              </div>
            </div>
            <div class="items-center py-4 sm:grid sm:grid-cols-3">
              <span class="block text-sm font-medium text-gray-700 sm:mt-px">Liczba dni</span>
              <div
                class="inline-flex items-center py-2 px-4 mt-1 w-full max-w-lg text-gray-500 bg-gray-50 rounded-md border border-gray-300 sm:col-span-2 sm:mt-0 sm:text-sm"
              >
                {{ estimatedDays.length }}
              </div>
            </div>
            <div class="items-center py-4 sm:grid sm:grid-cols-3">
              <label
                for="comment"
                class="block text-sm font-medium text-gray-700"
              >
                Komentarz
                <span class="text-xs text-gray-500">
                  (opcjonalny)
                </span>
              </label>
              <div class="mt-1 sm:col-span-2 sm:mt-0">
                <textarea
                  id="comment"
                  v-model="form.comment"
                  rows="4"
                  class="block w-full max-w-lg rounded-md border-gray-300 focus:border-blumilk-500 focus:ring-blumilk-500 shadow-sm sm:text-sm"
                />
              </div>
            </div>
            <div
              v-if="can.skipFlow"
              class="items-center py-4 sm:grid sm:grid-cols-3"
            >
              <label
                for="flowSkipped"
                class="block text-sm font-medium text-gray-700"
              >
                Natychmiastowo zatwierdź wniosek
              </label>
              <div class="mt-2 sm:col-span-2 sm:mt-0">
                <Switch
                  id="flowSkipped"
                  v-model="form.flowSkipped"
                  :class="[form.flowSkipped ? 'bg-blumilk-500' : 'bg-gray-200', 'relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blumilk-500']"
                  dusk="flowSkipped"
                >
                  <span
                    :class="[form.flowSkipped ? 'translate-x-5' : 'translate-x-0', 'pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200']"
                  />
                </Switch>
              </div>
            </div>
          </div>
          <div class="flex justify-end py-3">
            <div class="space-x-3">
              <InertiaLink
                href="/vacation/requests"
                class="py-2 px-4 text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blumilk-500 focus:ring-offset-2 shadow-sm"
              >
                Anuluj
              </InertiaLink>
              <button
                type="submit"
                class="inline-flex justify-center py-2 px-4 text-sm font-medium text-white bg-blumilk-600 rounded-md border border-transparent focus:outline-none focus:ring-2 focus:ring-blumilk-500 focus:ring-offset-2 shadow-sm"
                :class="[form.processing || !isDirty ? 'disabled:opacity-60' : 'hover:bg-blumilk-700']"
                :disabled="form.processing || !isDirty"
                dusk="save-request-button"
              >
                Dodaj
              </button>
            </div>
          </div>
        </div>
      </form>
    </div>
    <div
      v-if="stats.limit > 0 "
      class="h-full bg-white shadow-md"
    >
      <div class="p-4 sm:px-6">
        <h2 class="text-lg font-medium leading-6 text-gray-900">
          <span v-if="auth.user.id !== form.user.id">
            Dane urlopowe dla: {{ form.user.name }}
          </span>
          <span v-else>
            Moje dane o urlopie
          </span>
        </h2>
      </div>
      <div class="px-6 pt-8 border-t border-gray-200">
        <VacationChart :stats="stats" />
      </div>
    </div>
  </div>
</template>
