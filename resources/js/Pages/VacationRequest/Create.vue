<template>
  <InertiaHead title="Złóż wniosek urlopowy" />
  <div class="bg-white sm:rounded-lg shadow-md">
    <div class="p-4 sm:px-6">
      <h2 class="text-lg leading-6 font-medium text-gray-900">
        Złóż wniosek urlopowy
      </h2>
    </div>
    <form
      class="border-t border-gray-200 px-6"
      @submit.prevent="createForm"
    >
      <Listbox
        v-model="form.vacationType"
        as="div"
        class="sm:grid sm:grid-cols-3 py-4 items-center"
      >
        <ListboxLabel class="block text-sm font-medium text-gray-700">
          Rodzaj wniosku
        </ListboxLabel>
        <div class="mt-1 relative sm:mt-0 sm:col-span-2">
          <ListboxButton
            class="bg-white relative w-full max-w-lg border rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-default sm:text-sm focus:ring-1"
            :class="{ 'border-red-300 text-red-900 focus:outline-none focus:ring-red-500 focus:border-red-500': form.errors.vacationType, 'focus:ring-blumilk-500 focus:border-blumilk-500 sm:text-sm border-gray-300': !form.errors.vacationType }"
          >
            <span class="block truncate">{{ form.vacationType.label }}</span>
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
                v-for="vacationType in vacationTypes"
                :key="vacationType.value"
                v-slot="{ active, selected }"
                as="template"
                :value="vacationType"
              >
                <li :class="[active ? 'text-white bg-blumilk-600' : 'text-gray-900', 'cursor-default select-none relative py-2 pl-3 pr-9']">
                  <span :class="[selected ? 'font-semibold' : 'font-normal', 'block truncate']">
                    {{ vacationType.label }}
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
            v-if="form.errors.vacationType"
            class="mt-2 text-sm text-red-600"
          >
            {{ form.errors.vacationType }}
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
            v-model="form.dateFrom"
            :config="fromInputConfig"
            placeholder="Wybierz datę"
            class="block w-full max-w-lg shadow-sm rounded-md sm:text-sm"
            :class="{ 'border-red-300 text-red-900 focus:outline-none focus:ring-red-500 focus:border-red-500': form.errors.dateFrom, 'focus:ring-blumilk-500 focus:border-blumilk-500 sm:text-sm border-gray-300': !form.errors.dateFrom }"
            @on-change="onFromChange"
          />
          <p
            v-if="form.errors.dateFrom"
            class="mt-2 text-sm text-red-600"
          >
            {{ form.errors.dateFrom }}
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
            v-model="form.dateTo"
            :config="toInputConfig"
            placeholder="Wybierz datę"
            class="block w-full max-w-lg shadow-sm rounded-md sm:text-sm"
            :class="{ 'border-red-300 text-red-900 focus:outline-none focus:ring-red-500 focus:border-red-500': form.errors.dateTo, 'focus:ring-blumilk-500 focus:border-blumilk-500 sm:text-sm border-gray-300': !form.errors.dateTo }"
            @on-change="onToChange"
          />
          <p
            v-if="form.errors.dateTo"
            class="mt-2 text-sm text-red-600"
          >
            {{ form.errors.dateTo }}
          </p>
        </div>
      </div>
      <div class="sm:grid sm:grid-cols-3 py-4 items-center">
        <span class="block text-sm font-medium text-gray-700 sm:mt-px">Liczba dni urlopu</span>
        <div class="mt-1 sm:mt-0 sm:col-span-2 w-full max-w-lg bg-gray-50 border border-gray-300 rounded-md px-4 py-2 inline-flex items-center text-gray-500 sm:text-sm">
          1
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
            class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full max-w-lg sm:text-sm border-gray-300 rounded-md"
          />
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
</template>

<script>
import {useForm} from '@inertiajs/inertia-vue3'
import FlatPickr from 'vue-flatpickr-component'
import {Listbox, ListboxButton, ListboxLabel, ListboxOption, ListboxOptions} from '@headlessui/vue'
import {CheckIcon, SelectorIcon} from '@heroicons/vue/solid'
import {reactive} from 'vue'

export default {
  name: 'VacationRequestCreate',
  components: {
    FlatPickr,
    Listbox,
    ListboxButton,
    ListboxLabel,
    ListboxOption,
    ListboxOptions,
    CheckIcon,
    SelectorIcon,
  },
  props: {
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
      dateFrom: null,
      dateTo: null,
      vacationType: props.vacationTypes[0],
      comment: null,
    })

    const disableDates = [
      date => (date.getDay() === 0 || date.getDay() === 6),
    ]

    const fromInputConfig = reactive({
      maxDate: null,
      disable: disableDates,
    })

    const toInputConfig = reactive({
      minDate: null,
      disable: disableDates,
    })

    return {
      form,
      fromInputConfig,
      toInputConfig,
    }
  },
  methods: {
    createForm() {
      this.form
        .transform(data => ({
          from: data.dateFrom,
          to: data.dateTo,
          type: data.vacationType.value,
          comment: data.comment,
        }))
        .post('/vacation-requests')
    },
    onFromChange(selectedDates, dateStr) {
      this.toInputConfig.minDate = dateStr
    },
    onToChange(selectedDates, dateStr) {
      this.fromInputConfig.maxDate = dateStr
    },
  },

}
</script>