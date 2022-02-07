<template>
  <InertiaHead title="Edytuj dzień wolny" />
  <div class="bg-white shadow-md">
    <div class="p-4 sm:px-6">
      <h2 class="text-lg leading-6 font-medium text-gray-900">
        Edytuj dzień wolny
      </h2>
      <p class="mt-1 text-sm text-gray-500">
        Użytkownik nie będzie miał możliwości wzięcia urlopu w dzień wolny.
      </p>
    </div>
    <form
      class="border-t border-gray-200 px-6"
      @submit.prevent="editHoliday"
    >
      <div class="sm:grid sm:grid-cols-3 py-4 items-center">
        <label
          for="name"
          class="block text-sm font-medium text-gray-700 sm:mt-px"
        >
          Nazwa
        </label>
        <div class="mt-1 sm:mt-0 sm:col-span-2">
          <input
            id="name"
            v-model="form.name"
            type="text"
            class="block w-full max-w-lg shadow-sm rounded-md sm:text-sm"
            :class="{ 'border-red-300 text-red-900 focus:outline-none focus:ring-red-500 focus:border-red-500': form.errors.name, 'focus:ring-blumilk-500 focus:border-blumilk-500 sm:text-sm border-gray-300': !form.errors.name }"
          >
          <p
            v-if="form.errors.name"
            class="mt-2 text-sm text-red-600"
          >
            {{ form.errors.name }}
          </p>
        </div>
      </div>
      <div class="sm:grid sm:grid-cols-3 py-4 items-center">
        <label
          for="date"
          class="block text-sm font-medium text-gray-700 sm:mt-px"
        >
          Data
        </label>
        <div class="mt-1 sm:mt-0 sm:col-span-2">
          <FlatPickr
            id="date"
            v-model="form.date"
            placeholder="Wybierz datę"
            class="block w-full max-w-lg shadow-sm rounded-md sm:text-sm"
            :class="{ 'border-red-300 text-red-900 focus:outline-none focus:ring-red-500 focus:border-red-500': form.errors.date, 'focus:ring-blumilk-500 focus:border-blumilk-500 sm:text-sm border-gray-300': !form.errors.date }"
          />
          <p
            v-if="form.errors.date"
            class="mt-2 text-sm text-red-600"
          >
            {{ form.errors.date }}
          </p>
        </div>
      </div>
      <div class="flex justify-end py-3">
        <div class="space-x-3">
          <InertiaLink
            href="/holidays"
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
import { useForm } from '@inertiajs/inertia-vue3'
import FlatPickr from 'vue-flatpickr-component'

export default {
  name: 'HolidayEdit',
  components: {
    FlatPickr,
  },
  props: {
    holiday: {
      type: Object,
      default: () => null,
    },
  },
  setup(props) {
    const form = useForm({
      name: props.holiday.name,
      date: props.holiday.date,
    })

    return { form }
  },
  methods: {
    editHoliday() {
      this.form
        .put(`/holidays/${this.holiday.id}`)
    },
  },
}
</script>
