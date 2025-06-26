<script setup>
import { useForm } from '@inertiajs/vue3'
import FlatPickr from 'vue-flatpickr-component'
import { Listbox, ListboxButton, ListboxLabel, ListboxOption, ListboxOptions, Switch } from '@headlessui/vue'
import { CheckIcon, ChevronUpDownIcon, XCircleIcon } from '@heroicons/vue/24/solid'
import { computed, reactive, ref, watch } from 'vue'
import axios from 'axios'
import VacationType from '@/Shared/VacationType.vue'
import { DateTime } from 'luxon'
import InertiaLink from '@/Shared/InertiaLink.vue'
import { useGlobalProps } from '@/Composables/useGlobalProps'
import AppLayout from '@/Shared/Layout/AppLayout.vue'

const props = defineProps({
  users: Object,
  holidays: Object,
  vacationUserId: [Number, null],
  types: Array,
  typesByUser: Object,
  vacationFromDate: [String, null],
})

const { auth } = useGlobalProps()

const form = useForm({
  users: [],
  from: props.vacationFromDate,
  to: props.vacationFromDate,
  vacationType: props.types[0],
  comment: null,
  flowSkipped: false,
})

let isDirty = ref(false)
const currentDate = DateTime.now()

watch(form, formData => {
  const { from, to } = formData.data()
  isDirty.value = formData.isDirty || from !== null || to !== null
}, { immediate: true, deep: true })

refreshEstimatedDays(form.from, form.to, form.vacationType)

const estimatedDays = ref([])

const year = computed(() => form.from === null ? currentDate.year : DateTime.fromISO(form.from).year)

const bulkErrors = computed(() => Object.entries(form.errors)
  .filter(([key]) => key.startsWith('vacationRequests.'))
  .map(([, value]) => value))

const fromInputConfig = reactive({})
const toInputConfig = reactive({})

const usersAvailableForType = computed(() => props.users.data.filter(item => {
  const types = props.typesByUser[item.id] ?? []

  return types.includes(form.vacationType.value)
}))

watch(() => form.vacationType, vacationType => {
  refreshEstimatedDays(form.from, form.to, vacationType)

  form.users = form.users.filter(item => {
    const types = props.typesByUser[item] ?? []

    return types.includes(form.vacationType.value)
  })
})

watch(year, () => {
  refreshEstimatedDays(form.from, form.to, form.vacationType)
})

function createForm() {
  form
    .transform(data => ({
      ...data,
      type: data.vacationType.value,
    }))
    .post('/vacation/requests/bulk')
}

function onFromChange(selectedDates, dateStr) {
  if (form.to === null) {
    form.to = dateStr

    return
  }

  refreshEstimatedDays(form.from, form.to, form.vacationType)
}

function onToChange(selectedDates, dateStr) {
  if (form.from === null) {
    form.from = dateStr

    return
  }

  refreshEstimatedDays(form.from, form.to, form.vacationType)
}

async function refreshEstimatedDays(from, to, vacationType) {
  if (from && to && vacationType) {
    const res = await axios.post(
      '/api/vacation/calculate-days',
      { vacationType: vacationType.value, from: from, to: to },
    )

    estimatedDays.value = res.data
  }
}

function selectAll() {
  form.users = usersAvailableForType.value.map(item => item.id)
}

function deselectAll() {
  form.users = []
}
</script>

<template>
  <AppLayout title="Dodaj kilka wniosków">
    <div class="mx-auto w-full max-w-7xl">
      <div class="flex flex-col h-full bg-white shadow-md xl:col-span-2">
        <div class="p-4 sm:px-6">
          <h2 class="text-lg font-medium leading-6 text-gray-900">
            Dodaj kilka wniosków
          </h2>
        </div>
        <form
          class="px-6 h-full border-t border-gray-200"
          @submit.prevent="createForm"
        >
          <div class="flex flex-col justify-around h-full">
            <div>
              <div
                v-if="bulkErrors.length"
                class="p-4 mt-2 bg-red-50 rounded-md"
              >
                <div class="flex">
                  <div class="shrink-0">
                    <XCircleIcon class="size-5 text-red-400" />
                  </div>
                  <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">
                      Dla następujących osób nie udało się utworzyć wniosku:
                    </h3>
                    <ul class="list list-disc space-y-0.5 mt-2">
                      <li v-for="(error, i) in bulkErrors" :key="i" class="text-sm text-red-700">
                        {{ error }}
                      </li>
                    </ul>
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
                  >
                    <span class="block truncate">
                      <VacationType :type="form.vacationType.value" />
                    </span>
                    <span class="flex absolute inset-y-0 right-0 items-center pr-2 pointer-events-none">
                      <ChevronUpDownIcon class="size-5 text-gray-400" />
                    </span>
                  </ListboxButton>
                  <transition
                    leave-active-class="transition ease-in duration-100"
                    leave-from-class="opacity-100"
                    leave-to-class="opacity-0"
                  >
                    <ListboxOptions
                      class="overflow-auto absolute z-10 py-1 mt-1 w-full max-w-lg max-h-60 text-base bg-white rounded-md focus:outline-none ring-1 ring-black/5 shadow-lg sm:text-sm"
                    >
                      <ListboxOption
                        v-for="vacationType in types"
                        :key="vacationType.value"
                        v-slot="{ active, selected }"
                        as="template"
                        :value="vacationType"
                      >
                        <li :class="[active ? 'bg-gray-100' : 'text-gray-900', 'cursor-default select-none relative py-2 pl-3 pr-9']">
                          <span :class="[selected ? 'font-semibold' : 'font-normal', 'block truncate']">
                            <VacationType :type="vacationType.value" />
                          </span>

                          <span
                            v-if="selected"
                            :class="['text-blumilk-600 absolute inset-y-0 right-0 flex items-center pr-4']"
                          >
                            <CheckIcon class="size-5" />
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
                <div class="mt-1 sm:col-span-2 sm:mt-0">
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
                <div class="mt-1 sm:col-span-2 sm:mt-0">
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
                v-if="auth.can.skipRequestFlow"
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
                    :class="[form.flowSkipped ? 'bg-blumilk-500' : 'bg-gray-200', 'relative inline-flex shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blumilk-500']"
                  >
                    <span
                      :class="[form.flowSkipped ? 'translate-x-5' : 'translate-x-0', 'pointer-events-none inline-block size-5 rounded-full bg-white shadow ring-0 transition ease-in-out duration-200']"
                    />
                  </Switch>
                </div>
              </div>
              <div class="py-4">
                <label for="users"
                       class="flex items-center jusftiy-between w-full text-sm font-medium text-gray-700 pb-4 px-2"
                >
                  <span class="flex-1">
                    Wybrane osoby
                  </span>
                  <input
                    type="checkbox"
                    :checked="form.users.length === usersAvailableForType.length"
                    class="col-start-1 row-start-1 size-5 appearance-none rounded border border-gray-300 bg-white checked:border-blumilk-600 checked:bg-blumilk-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blumilk-600"
                    @change="$event.target.checked ? selectAll() : deselectAll()"
                  >
                </label>
                <ul class="divide-y divide-gray-200">
                  <li v-for="user in usersAvailableForType" :key="user.id">
                    <label
                      :class="['flex justify-between items-center cursor-pointer py-3 px-2', form.users.includes(user.id) && 'bg-blumilk-25']"
                      :for="`user-${user.id}`"
                    >
                      <span class="flex justify-start items-center">
                        <span class="inline-flex justify-center items-center size-10 rounded-full">
                          <img
                            :src="user.avatar"
                            class="size-10 rounded-full"
                          >
                        </span>
                        <span class="ml-3">
                          <span class="text-sm font-medium text-gray-900 whitespace-nowrap">
                            {{ user.name }}
                          </span>
                        </span>
                      </span>
                      <input
                        :id="`user-${user.id}`"
                        v-model="form.users"
                        :name="`user-${user.id}`"
                        type="checkbox"
                        :value="user.id"
                        class="col-start-1 row-start-1 size-5 appearance-none rounded border border-gray-300 bg-white checked:border-blumilk-600 checked:bg-blumilk-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blumilk-600"
                      >
                    </label>
                  </li>
                </ul>
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
                >
                  Dodaj
                </button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>
