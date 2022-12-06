<script setup>
import { useForm } from '@inertiajs/inertia-vue3'
import FlatPickr from 'vue-flatpickr-component'

const props = defineProps({
  holiday: Object,
})

const form = useForm({
  name: props.holiday.name,
  date: props.holiday.date,
})

function editHoliday() {
  form.put(`/holidays/${props.holiday.id}`)
}
</script>

<template>
  <InertiaHead title="Edytuj dzień wolny" />
  <div class="mx-auto w-full max-w-7xl bg-white shadow-md">
    <div class="p-4 sm:px-6">
      <h2 class="text-lg font-medium leading-6 text-gray-900">
        Edytuj dzień wolny
      </h2>
    </div>
    <form
      class="px-6 border-t border-gray-200"
      @submit.prevent="editHoliday"
    >
      <div class="items-center py-4 sm:grid sm:grid-cols-3">
        <label
          for="name"
          class="block text-sm font-medium text-gray-700 sm:mt-px"
        >
          Nazwa
        </label>
        <div class="mt-1 sm:col-span-2 sm:mt-0">
          <input
            id="name"
            v-model="form.name"
            type="text"
            class="block w-full max-w-lg rounded-md shadow-sm sm:text-sm"
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
      <div class="items-center py-4 sm:grid sm:grid-cols-3">
        <label
          for="date"
          class="block text-sm font-medium text-gray-700 sm:mt-px"
        >
          Data
        </label>
        <div class="mt-1 sm:col-span-2 sm:mt-0">
          <FlatPickr
            id="date"
            v-model="form.date"
            class="block w-full max-w-lg rounded-md shadow-sm sm:text-sm"
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
            class="py-2 px-4 text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blumilk-500 focus:ring-offset-2 shadow-sm"
          >
            Anuluj
          </InertiaLink>
          <button
            type="submit"
            class="inline-flex justify-center py-2 px-4 text-sm font-medium text-white bg-blumilk-600 rounded-md border border-transparent focus:outline-none focus:ring-2 focus:ring-blumilk-500 focus:ring-offset-2 shadow-sm"
            :class="[form.processing || !form.isDirty ? 'disabled:opacity-60' : 'hover:bg-blumilk-700']"
            :disabled="form.processing || !form.isDirty"
          >
            Zapisz
          </button>
        </div>
      </div>
    </form>
  </div>
</template>
