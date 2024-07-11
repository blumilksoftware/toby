<script setup>
import { Switch } from '@headlessui/vue'
import { useForm } from '@inertiajs/inertia-vue3'
import { ref, watch } from 'vue'
import VacationLimitPopup from '@/Shared/VacationLimitPopup.vue'
import RemainingFromPreviousYearPopup from '@/Shared/RemainingFromPreviousYearPopup.vue'
import TakeVacationDaysFromPreviousYearModal from '@/Shared/Modals/TakeVacationDaysFromPreviousYearModal.vue'

const props = defineProps({
  limits: Object,
  years: Object,
})

const form = useForm({
  items: props.limits,
})

const takingDaysFromPreviousYear = ref(false)
const limitToChange = ref(null)

function submitVacationDays() {
  form
    .transform(data => ({
      items: data.items.map(item => ({
        id: item.id,
        days: item.hasVacation ? item.days : null,
      })),
    }))
    .put('/vacation/limits', {
      preserveState: (page) => Object.keys(page.props.errors).length,
      preserveScroll: true,
    })
}

watch(() => form.items, () => {
  for (const item of form.items) {
    item.limit = item.days + item.fromPreviousYear - item.toNextYear
  }

}, { deep: true })
</script>

<template>
  <InertiaHead title="Dostępne dni urlopu dla użytkowników" />
  <div class="bg-white shadow-md">
    <div class="flex justify-between items-center p-4 sm:px-6">
      <div>
        <h2 class="text-lg font-medium leading-6 text-gray-900">
          Limit dni urlopu dla użytkowników
        </h2>
      </div>
    </div>
    <div class="border-t border-gray-200">
      <form @submit.prevent="submitVacationDays">
        <div class="overflow-auto xl:overflow-visible">
          <table class="min-w-full border-b divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th
                  class="py-3 px-4 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase whitespace-nowrap"
                  scope="col"
                >
                  Imię i nazwisko
                </th>
                <th
                  class="py-3 px-4 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase whitespace-nowrap"
                  scope="col"
                >
                  Forma zatrudnienia
                </th>
                <th
                  class="py-3 px-4 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase whitespace-nowrap"
                  scope="col"
                >
                  Posiada limit?
                </th>
                <th
                  class="py-3 px-4 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase whitespace-nowrap"
                  scope="col"
                >
                  Pozostałe dni z {{ years.selected.year - 1 }}
                </th>
                <th
                  class="py-3 px-4 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase whitespace-nowrap"
                  scope="col"
                >
                  Przysługujące dni w roku {{ years.selected.year }}
                </th>
                <th
                  class="py-3 px-4 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase whitespace-nowrap"
                  scope="col"
                >
                  Łączny limit na rok {{ years.selected.year }}
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
              <tr
                v-for="(item, index) in form.items"
                :key="item.id"
                class="hover:bg-blumilk-25"
              >
                <td class="p-4 text-sm text-gray-500 whitespace-nowrap">
                  <InertiaLink :href="`/users/${item.user.id}`">
                    <div class="flex">
                      <span class="inline-flex justify-center items-center w-10 h-10 rounded-full">
                        <img
                          :src="item.user.avatar"
                          class="w-10 h-10 rounded-full"
                        >
                      </span>
                      <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900 break-all">
                          {{ item.user.name }}
                        </p>
                        <p class="text-sm text-gray-500 break-all">
                          {{ item.user.email }}
                        </p>
                      </div>
                    </div>
                  </InertiaLink>
                </td>
                <td class="p-4 text-sm text-gray-500 whitespace-nowrap">
                  {{ item.user.employmentForm }}
                </td>
                <td class="p-4 text-sm text-gray-500 whitespace-nowrap">
                  <Switch
                    v-model="item.hasVacation"
                    :class="[item.hasVacation ? 'bg-blumilk-500' : 'bg-gray-200', 'relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blumilk-500']"
                  >
                    <span
                      :class="[item.hasVacation ? 'translate-x-5' : 'translate-x-0', 'pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200']"
                    />
                  </Switch>
                </td>
                <td class="p-4 text-sm text-gray-500 whitespace-nowrap">
                  <RemainingFromPreviousYearPopup
                    :remaining="item.remainingLastYear"
                    :to-next-year="item.fromPreviousYear"
                    :year="years.selected.year - 1"
                    class="w-full"
                    @change-previous-year="limitToChange = item; takingDaysFromPreviousYear = true"
                  >
                    <div
                      class="inline-flex items-center py-2 px-4 mt-1 w-full max-w-lg text-gray-500 bg-gray-50 rounded-md border border-gray-300 sm:col-span-2 sm:mt-0 sm:text-sm"
                    >
                      {{ item.remainingLastYear + item.fromPreviousYear }}
                    </div>
                  </RemainingFromPreviousYearPopup>
                </td>
                <td class="p-4 text-sm text-gray-500 whitespace-nowrap">
                  <div class="mt-1 sm:col-span-2 sm:mt-0">
                    <input
                      v-model="item.days"
                      :class="{ 'border-red-300 text-red-900 focus:outline-none focus:ring-red-500 focus:border-red-500': form.errors[`items.${index}.days`], 'focus:ring-blumilk-500 focus:border-blumilk-500 sm:text-sm border-gray-300': !form.errors[`items.${index}.days`] }"
                      :disabled="!item.hasVacation"
                      class="block w-full disabled:text-slate-500 disabled:bg-slate-50 rounded-md disabled:border-slate-200 shadow-sm disabled:shadow-none disabled:cursor-not-allowed sm:text-sm"
                      min="0"
                      type="number"
                    >
                    <p
                      v-if="form.errors[`items.${index}.days`]"
                      class="mt-2 text-sm text-red-600"
                    >
                      {{ form.errors[`items.${index}.days`] }}
                    </p>
                  </div>
                </td>
                <td class="p-4 text-sm text-gray-500 whitespace-nowrap">
                  <VacationLimitPopup
                    :days="item.days"
                    :from-previous-year="item.fromPreviousYear"
                    :limit="item.limit"
                    :to-next-year="item.toNextYear"
                    :year="years.selected.year"
                    class="w-full"
                  >
                    <div
                      class="inline-flex items-center py-2 px-4 mt-1 w-full max-w-lg text-gray-500 bg-gray-50 rounded-md border border-gray-300 sm:col-span-2 sm:mt-0 sm:text-sm"
                    >
                      {{ item.limit }}
                    </div>
                  </VacationLimitPopup>
                </td>
              </tr>
              <tr v-if="!form.items.length">
                <td
                  class="py-4 text-xl leading-5 text-center text-gray-700"
                  colspan="100%"
                >
                  Brak danych
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="flex justify-end py-3 px-4">
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
  </div>
  <TakeVacationDaysFromPreviousYearModal
    :limit="limitToChange"
    :show="takingDaysFromPreviousYear"
    :year="years.selected.year - 1"
    @changed="takingDaysFromPreviousYear = false"
    @close="takingDaysFromPreviousYear = false"
  />
</template>
