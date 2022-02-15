<template>
  <InertiaHead title="Użytkownicy" />
  <div class="bg-white shadow-md">
    <div class="flex justify-between items-center p-4 sm:px-6">
      <div>
        <h2 class="text-lg leading-6 font-medium text-gray-900">
          Dostępne dni urlopu dla użytkowników
        </h2>
        <p class="mt-1 text-sm text-gray-500">
          Zarządzaj dostepnymi dniami urlopów dla użytkowników.
        </p>
      </div>
    </div>
    <div class="border-t border-gray-200">
      <div class="overflow-x-auto xl:overflow-x-visible overflow-y-auto xl:overflow-y-visible">
        <form @submit.prevent="submitVacationDays">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th
                  scope="col"
                  class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider"
                >
                  Imię i nazwisko
                </th>
                <th
                  scope="col"
                  class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider"
                >
                  Forma zatrudnienia
                </th>
                <th
                  scope="col"
                  class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider"
                >
                  Posiada urlop?
                </th>
                <th
                  scope="col"
                  class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider"
                >
                  Dostępne dni w roku
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
              <tr
                v-for="(item, index) in form.items"
                :key="item.id"
                class="hover:bg-blumilk-25"
              >
                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                  <div class="flex">
                    <span
                      class="inline-flex items-center justify-center h-10 w-10 rounded-full"
                    >
                      <img
                        class="h-10 w-10 rounded-full"
                        :src="item.user.avatar"
                        alt=""
                      >
                    </span>
                    <div class="ml-3">
                      <p class="text-sm font-medium break-all text-gray-900">
                        {{ item.user.name }}
                      </p>
                      <p class="text-sm break-all text-gray-500">
                        {{ item.user.email }}
                      </p>
                    </div>
                  </div>
                </td>
                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ item.user.employmentForm }}
                </td>
                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                  <Switch
                    v-model="item.hasVacation"
                    :class="[item.hasVacation ? 'bg-blumilk-500' : 'bg-gray-200', 'relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blumilk-500']"
                  >
                    <span
                      :class="[item.hasVacation ? 'translate-x-5' : 'translate-x-0', 'pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200']"
                    />
                  </Switch>
                </td>
                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                  <div class="mt-1 sm:mt-0 sm:col-span-2">
                    <input
                      v-model="item.days"
                      type="number"
                      min="0"
                      class="block w-full shadow-sm rounded-md sm:text-sm disabled:bg-slate-50 disabled:text-slate-500 disabled:border-slate-200 disabled:shadow-none disabled:cursor-not-allowed"
                      :disabled="!item.hasVacation"
                      :class="{ 'border-red-300 text-red-900 focus:outline-none focus:ring-red-500 focus:border-red-500': form.errors[`items.${index}.days`], 'focus:ring-blumilk-500 focus:border-blumilk-500 sm:text-sm border-gray-300': !form.errors[`items.${index}.days`] }"
                    >
                    <p
                      v-if="form.errors[`items.${index}.days`]"
                      class="mt-2 text-sm text-red-600"
                    >
                      {{ form.errors[`items.${index}.days`] }}
                    </p>
                  </div>
                </td>
              </tr>
              <tr
                v-if="!form.items.length"
              >
                <td
                  colspan="100%"
                  class="text-center py-4 text-xl leading-5 text-gray-700"
                >
                  Brak danych
                </td>
              </tr>
            </tbody>
          </table>
          <div class="flex justify-end py-3 px-4">
            <button
              type="submit"
              class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blumilk-600 hover:bg-blumilk-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blumilk-500"
            >
              Zapisz
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import {Switch} from '@headlessui/vue'
import {useForm} from '@inertiajs/inertia-vue3'

export default {
  name: 'VacationLimits',
  components: {
    Switch,
  },
  props: {
    limits: {
      type: Object,
      default: () => null,
    },
    years: {
      type: Object,
      default: () => null,
    },
  },
  setup(props) {
    const form = useForm({
      items: props.limits.data,
    })

    return {
      form,
    }
  },
  methods: {
    submitVacationDays() {
      this.form
        .transform(data => ({
          items: data.items.map(item => ({
            id: item.id,
            days: item.hasVacation ? item.days : null,
          })),
        }))
        .put('/vacation-limits', {
          preserveState: (page) => Object.keys(page.props.errors).length,
          preserveScroll: true,
        })
    },
  },
}
</script>
