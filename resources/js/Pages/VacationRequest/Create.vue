<template>
  <InertiaHead title="Złóż wniosek urlopowy" />
  <div class="grid grid-cols-1 gap-4 items-start lg:grid-cols-3 lg:gap-8">
    <div class="grid grid-cols-1 lg:col-span-2 bg-white shadow-md">
      <div class="p-4 sm:px-6">
        <h2 class="text-lg leading-6 font-medium text-gray-900">
          Złóż wniosek urlopowy
        </h2>
      </div>
      <form
        class="border-t border-gray-200 px-6"
        @submit.prevent="createForm"
      >
        <div
          v-if="form.errors.vacationRequest"
          class="rounded-md bg-red-50 p-4 mt-2"
        >
          <div class="flex">
            <div class="flex-shrink-0">
              <XCircleIcon class="h-5 w-5 text-red-400" />
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
          v-model="form.user"
          as="div"
          class="sm:grid sm:grid-cols-3 py-4 items-center"
        >
          <ListboxLabel class="block text-sm font-medium text-gray-700">
            Osoba składająca wniosek
          </ListboxLabel>
          <div class="mt-1 relative sm:mt-0 sm:col-span-2">
            <ListboxButton
              class="bg-white relative w-full max-w-lg border rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-default sm:text-sm focus:ring-1"
              :class="{ 'border-red-300 text-red-900 focus:outline-none focus:ring-red-500 focus:border-red-500': form.errors.user, 'focus:ring-blumilk-500 focus:border-blumilk-500 sm:text-sm border-gray-300': !form.errors.user }"
            >
              <span class="flex items-center">
                <img
                  :src="form.user.avatar"
                  class="flex-shrink-0 h-6 w-6 rounded-full"
                >
                <span class="ml-3 block truncate">{{ form.user.name }}</span>
              </span>
              <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                <SelectorIcon class="h-5 w-5 text-gray-400" />
              </span>
            </ListboxButton>

            <transition
              leave-active-class="transition ease-in duration-100"
              leave-from-class="opacity-100"
              leave-to-class="opacity-0"
            >
              <ListboxOptions
                class="absolute z-10 mt-1 w-full max-w-lg bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm"
              >
                <ListboxOption
                  v-for="user in users.data"
                  :key="user.id"
                  v-slot="{ active, selected }"
                  as="template"
                  :value="user"
                >
                  <li
                    :class="[active ? 'text-white bg-blumilk-600' : 'text-gray-900', 'cursor-default select-none relative py-2 pl-3 pr-9']"
                  >
                    <div class="flex items-center">
                      <img
                        :src="user.avatar"
                        alt=""
                        class="flex-shrink-0 h-6 w-6 rounded-full"
                      >
                      <span :class="[selected ? 'font-semibold' : 'font-normal', 'ml-3 block truncate']">
                        {{ user.name }}
                      </span>
                    </div>

                    <span
                      v-if="selected"
                      :class="[active ? 'text-white' : 'text-blumilk-600', 'absolute inset-y-0 right-0 flex items-center pr-4']"
                    >
                      <CheckIcon class="h-5 w-5" />
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
        <Listbox
          v-model="form.type"
          as="div"
          class="sm:grid sm:grid-cols-3 py-4 items-center"
        >
          <ListboxLabel class="block text-sm font-medium text-gray-700">
            Rodzaj wniosku
          </ListboxLabel>
          <div class="mt-1 relative sm:mt-0 sm:col-span-2">
            <ListboxButton
              class="bg-white relative w-full max-w-lg border rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-default sm:text-sm focus:ring-1"
              :class="{ 'border-red-300 text-red-900 focus:outline-none focus:ring-red-500 focus:border-red-500': form.errors.type, 'focus:ring-blumilk-500 focus:border-blumilk-500 sm:text-sm border-gray-300': !form.errors.type }"
            >
              <span class="block truncate">{{ form.type.label }}</span>
              <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                <SelectorIcon class="h-5 w-5 text-gray-400" />
              </span>
            </ListboxButton>

            <transition
              leave-active-class="transition ease-in duration-100"
              leave-from-class="opacity-100"
              leave-to-class="opacity-0"
            >
              <ListboxOptions
                class="absolute z-10 mt-1 w-full max-w-lg bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm"
              >
                <ListboxOption
                  v-for="type in vacationTypes"
                  :key="type.value"
                  v-slot="{ active, selected }"
                  as="template"
                  :value="type"
                >
                  <li
                    :class="[active ? 'text-white bg-blumilk-600' : 'text-gray-900', 'cursor-default select-none relative py-2 pl-3 pr-9']"
                  >
                    <span :class="[selected ? 'font-semibold' : 'font-normal', 'block truncate']">
                      {{ type.label }}
                    </span>

                    <span
                      v-if="selected"
                      :class="[active ? 'text-white' : 'text-blumilk-600', 'absolute inset-y-0 right-0 flex items-center pr-4']"
                    >
                      <CheckIcon class="h-5 w-5" />
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
        <div class="sm:grid sm:grid-cols-3 py-4 items-center">
          <label
            for="date_from"
            class="block text-sm font-medium text-gray-700 sm:mt-px"
          >
            Planowany urlop od
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <FlatPickr
              id="date_from"
              v-model="form.from"
              :config="fromInputConfig"
              placeholder="Wybierz datę"
              class="block w-full max-w-lg shadow-sm rounded-md sm:text-sm"
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
        <div class="sm:grid sm:grid-cols-3 py-4 items-center">
          <label
            for="date_from"
            class="block text-sm font-medium text-gray-700 sm:mt-px"
          >
            Planowany urlop do
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <FlatPickr
              id="date_to"
              v-model="form.to"
              :config="toInputConfig"
              placeholder="Wybierz datę"
              class="block w-full max-w-lg shadow-sm rounded-md sm:text-sm"
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
        <div class="sm:grid sm:grid-cols-3 py-4 items-center">
          <span class="block text-sm font-medium text-gray-700 sm:mt-px">Liczba dni urlopu</span>
          <div
            class="mt-1 sm:mt-0 sm:col-span-2 w-full max-w-lg bg-gray-50 border border-gray-300 rounded-md px-4 py-2 inline-flex items-center text-gray-500 sm:text-sm"
          >
            {{ estimatedDays.length }}
          </div>
        </div>
        <div class="sm:grid sm:grid-cols-3 py-4 items-center">
          <label
            for="comment"
            class="block text-sm font-medium text-gray-700"
          >
            Komentarz
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <textarea
              id="comment"
              v-model="form.comment"
              rows="4"
              class="shadow-sm focus:ring-blumilk-500 focus:border-blumilk-500 block w-full max-w-lg sm:text-sm border-gray-300 rounded-md"
            />
          </div>
        </div>
        <div class="sm:grid sm:grid-cols-3 py-4 items-center">
          <label
            for="flowSkipped"
            class="block text-sm font-medium text-gray-700"
          >
            Natychmiastowo zatwierdź wniosek
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <Switch
              id="flowSkipped"
              v-model="form.flowSkipped"
              :class="[form.flowSkipped ? 'bg-blumilk-500' : 'bg-gray-200', 'relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blumilk-500']"
            >
              <span
                :class="[form.flowSkipped ? 'translate-x-5' : 'translate-x-0', 'pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200']"
              />
            </Switch>
          </div>
        </div>
        <div class="flex justify-end py-3">
          <div class="space-x-3">
            <InertiaLink
              href="/vacation-requests"
              class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blumilk-500"
            >
              Anuluj
            </InertiaLink>
            <button
              type="submit"
              :disabled="form.processing"
              class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blumilk-600 hover:bg-blumilk-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blumilk-500"
            >
              Zapisz
            </button>
          </div>
        </div>
      </form>
    </div>
    <div class="grid grid-cols-1 bg-white shadow-md h-full">
      <div>
        <div class="p-4 sm:px-6">
          <h2 class="text-lg leading-6 font-medium text-gray-900">
            Urlop wypoczynkowy
          </h2>
        </div>
        <div class="border-t border-gray-200 px-6 pt-8">
          <VacationChart :stats="stats" />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import {useForm} from '@inertiajs/inertia-vue3'
import FlatPickr from 'vue-flatpickr-component'
import {Listbox, ListboxButton, ListboxLabel, ListboxOption, ListboxOptions, Switch} from '@headlessui/vue'
import {CheckIcon, SelectorIcon, XCircleIcon} from '@heroicons/vue/solid'
import {reactive, ref, watch} from 'vue'
import axios from 'axios'
import useCurrentYearPeriodInfo from '@/Composables/yearPeriodInfo'
import VacationChart from '@/Shared/VacationChart'

export default {
  name: 'VacationRequestCreate',
  components: {
    VacationChart,
    Switch,
    FlatPickr,
    Listbox,
    ListboxButton,
    ListboxLabel,
    ListboxOption,
    ListboxOptions,
    CheckIcon,
    SelectorIcon,
    XCircleIcon,
  },
  props: {
    auth: {
      type: Object,
      default: () => null,
    },
    users: {
      type: Object,
      default: () => null,
    },
    vacationTypes: {
      type: Object,
      default: () => null,
    },
    holidays: {
      type: Object,
      default: () => null,
    },
  },
  setup(props) {
    const form = useForm({
      user: props.users.data.find(user => user.id === props.auth.user.id),
      from: null,
      to: null,
      type: props.vacationTypes[0],
      comment: null,
      flowSkipped: false,
    })

    const estimatedDays = ref([])

    const stats = ref({
      used: 0,
      pending: 0,
      remaining: 0,
    })

    const {minDate, maxDate} = useCurrentYearPeriodInfo()

    const disableDates = [
      date => (date.getDay() === 0 || date.getDay() === 6),
    ]

    const fromInputConfig = reactive({
      minDate: minDate,
      maxDate: maxDate,
      disable: disableDates,
    })

    const toInputConfig = reactive({
      minDate: minDate,
      maxDate: maxDate,
      disable: disableDates,
    })

    watch(() => form.user, user => {
      axios.post('/api/calculate-vacations-stats', {user: user.id})
        .then(res => stats.value = res.data)
    }, {immediate: true})

    return {
      form,
      estimatedDays,
      stats,
      fromInputConfig,
      toInputConfig,
    }
  },
  methods: {
    createForm() {
      this.form
        .transform(data => ({
          ...data,
          type: data.type.value,
          user: data.user.id,
        }))
        .post('/vacation-requests')
    },
    onFromChange(selectedDates, dateStr) {
      this.form.to = dateStr

      this.refreshEstimatedDays(this.form.from, this.form.to)
    },
    onToChange() {
      this.refreshEstimatedDays(this.form.from, this.form.to)
    },
    refreshEstimatedDays(from, to) {
      if (from && to) {
        axios.post('/api/calculate-vacation-days', {from, to})
          .then(res => this.estimatedDays = res.data)
      }
    },
    updateVacationStats(user) {
      console.log(user)
      axios.post('/api/get-vacations-stats')
        .then(res => this.stats = res.data)
    },
  },

}
</script>
