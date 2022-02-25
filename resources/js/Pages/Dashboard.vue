<template>
  <InertiaHead title="Strona główna" />
  <div class="grid grid-cols-1 gap-4 items-start lg:grid-cols-3 lg:gap-8">
    <div class="grid grid-cols-1 gap-4 lg:col-span-2">
      <section>
        <div class=" bg-white overflow-hidden shadow">
          <div class="bg-white p-6">
            <div class="sm:flex sm:items-center sm:justify-between">
              <div class="sm:flex sm:space-x-5">
                <div class="flex-shrink-0">
                  <img
                    class="mx-auto h-20 w-20 rounded-full"
                    :src="user.avatar"
                    alt=""
                  >
                </div>
                <div class="mt-4 text-center sm:mt-0 sm:pt-1 sm:text-left">
                  <p class="text-sm font-medium text-gray-600">
                    Cześć,
                  </p>
                  <p class="text-xl font-bold text-gray-900 sm:text-2xl">
                    {{ user.name }}
                  </p>
                  <p class="text-sm font-medium text-gray-600">
                    {{ user.role }}
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
      <section>
        <div class="grid grid-cols-2 gap-4">
          <div class="bg-white shadow-md p-4">
            <VacationChart :stats="stats" />
          </div>
          <div class="h-full">
            <div class="grid grid-cols-2 gap-4 h-full">
              <div class="px-4 py-5 bg-white shadow-md sm:p-6">
                <dt class="text-sm font-medium text-gray-500 truncate">
                  Limit urlopów
                </dt>
                <dd class="mt-1 text-3xl font-semibold text-gray-900">
                  {{ stats.limit }}
                </dd>
              </div>
              <div class="px-4 py-5 bg-white shadow-md sm:p-6">
                <dt class="text-sm font-medium text-gray-500 truncate">
                  Dni do wykorzystania
                </dt>
                <dd class="mt-1 text-3xl font-semibold text-gray-900">
                  {{ stats.remaining }}
                </dd>
              </div>
              <div class="px-4 py-5 bg-white shadow-md sm:p-6">
                <dt class="text-sm font-medium text-gray-500 truncate">
                  Dni wykorzystane
                </dt>
                <dd class="mt-1 text-3xl font-semibold text-gray-900">
                  {{ stats.used }}
                </dd>
              </div>
              <div class="px-4 py-5 bg-white shadow-md sm:p-6">
                <dt class="text-sm font-medium text-gray-500 truncate">
                  Inne urlopy
                </dt>
                <dd class="mt-1 text-3xl font-semibold text-gray-900">
                  {{ stats.other }}
                </dd>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
    <div class="grid grid-cols-1 gap-4">
      <section>
        <div class="bg-white shadow-md">
          <div class="p-4 sm:px-6">
            <h2 class="text-lg leading-6 font-medium text-gray-900">
              Twoje wnioski
            </h2>
          </div>
          <div class="border-t border-gray-200 pb-5 px-4 sm:px-6">
            <div class="flow-root mt-6">
              <ul class="-my-5 divide-y divide-gray-200">
                <li
                  v-for="request in vacationRequests.data"
                  :key="request.id"
                  class="py-5"
                >
                  <div class="relative focus-within:ring-2 focus-within:ring-cyan-500">
                    <h3 class="text-sm font-semibold text-blumilk-600 hover:text-blumilk-500">
                      <InertiaLink
                        :href="`/vacation-requests/${request.id}`"
                        class="hover:underline focus:outline-none"
                      >
                        <span class="absolute inset-0" />
                        Wniosek o {{ request.type.toLowerCase() }}
                        [{{ request.name }}]
                      </InertiaLink>
                    </h3>
                    <p class="mt-1 text-sm text-gray-600">
                      {{ request.from }} - {{ request.to }}
                    </p>
                    <p class="mt-2 text-sm text-gray-600">
                      <Status :status="request.state" />
                    </p>
                  </div>
                </li>
              </ul>
            </div>
            <div class="mt-6">
              <InertiaLink
                href="/vacation-requests"
                class="w-full flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
              >
                Zobacz wszystkie
              </InertiaLink>
            </div>
          </div>
        </div>
      </section>
      <section>
        <div class="bg-white shadow-md">
          <div class="p-4 sm:px-6">
            <h2 class="text-lg leading-6 font-medium text-gray-900">
              Dzisiejsze nieobecności
            </h2>
          </div>
          <div class="border-t border-gray-200 px-4 sm:px-6">
            <ul class="divide-y divide-gray-200">
              <li
                v-for="absence in absences.data"
                :key="absence.user.id"
                class="py-4 flex"
              >
                <img
                  class="h-10 w-10 rounded-full"
                  :src="absence.user.avatar"
                >
                <div class="ml-3">
                  <p class="text-sm font-medium text-gray-900">
                    {{ absence.user.name }}
                  </p>
                  <p class="text-sm text-gray-500">
                    {{ absence.user.email }}
                  </p>
                </div>
              </li>
            </ul>
          </div>
        </div>
      </section>
      <section>
        <div class="bg-white shadow-md">
          <div>
            <div class="p-4 sm:px-6">
              <h2 class="text-lg leading-6 font-medium text-gray-900">
                Najbliższe dni wolne
              </h2>
            </div>
            <div class="border-t border-gray-200 px-4 pb-5 sm:px-6">
              <ul class="divide-y divide-gray-200">
                <li
                  v-for="holiday in holidays.data"
                  :key="holiday.id.id"
                  class="py-4 flex"
                >
                  <div>
                    <p class="text-sm font-medium text-gray-900">
                      {{ holiday.name }}
                    </p>
                    <p class="text-sm text-gray-500">
                      {{ holiday.displayDate }}
                    </p>
                  </div>
                </li>
              </ul>
              <div>
                <InertiaLink
                  href="/holidays"
                  class="w-full flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                >
                  Zobacz wszystkie
                </InertiaLink>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </div>
</template>

<script>
import {computed} from 'vue'
import {usePage} from '@inertiajs/inertia-vue3'
import Status from '@/Shared/Status'
import VacationChart from '@/Shared/VacationChart'

export default {
  name: 'DashboardPage',
  components: {Status, VacationChart},
  props: {
    absences: {
      type: Object,
      default: null,
    },
    vacationRequests: {
      type: Object,
      default: null,
    },
    holidays: {
      type: Object,
      default: null,
    },
    stats: {
      type: Object,
      default: () => ({
        used: 0,
        pending: 0,
        other: 0,
      }),
    },
  },
  setup() {
    const user = computed(() => usePage().props.value.auth.user)

    return {
      user,
    }
  },
}
</script>
