<script setup>
import { PaperClipIcon } from '@heroicons/vue/24/outline'
import Activity from '@/Shared/Activity.vue'
import Status from '@/Shared/Status.vue'
import VacationType from '@/Shared/VacationType.vue'
import VacationBar from '@/Shared/VacationBar.vue'
import CalendarComponent from '@/Shared/CalendarComponent.vue'
import { ref, watch } from 'vue'
import UserProfileLink from '@/Shared/UserProfileLink.vue'
import InertiaLink from '@/Shared/InertiaLink.vue'
import { useGlobalProps } from '@/Composables/useGlobalProps'
import AppLayout from '@/Shared/Layout/AppLayout.vue'

const props = defineProps({
  request: Object,
  activities: Object,
  stats: Object,
  handyCalendarData: Object,
})

const { auth } = useGlobalProps()

const handyCalendarKey = ref(0)

const forceRerender = () => {
  handyCalendarKey.value += 1
}

watch(() => props.handyCalendarData, () => {
  forceRerender()
})
</script>

<template>
  <AppLayout :title="`Wniosek ${request.name}`">
    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3 xl:grid-flow-col-dense">
      <div class="space-y-6 xl:col-span-2 xl:col-start-1">
        <div class="bg-white shadow-md">
          <div class="py-5 px-4 sm:px-6">
            <h3 class="text-lg font-medium leading-6 text-gray-900">
              Informacje na temat wniosku
            </h3>
          </div>
          <div class="py-5 px-4 border-t border-gray-200 sm:p-0">
            <dl class="sm:divide-y sm:divide-gray-200">
              <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:py-5 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">
                  Nr wniosku
                </dt>
                <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                  {{ request.name }}
                </dd>
              </div>
              <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:py-5 sm:px-6">
                <dt class="flex items-center text-sm font-medium text-gray-500">
                  Pracownik
                </dt>
                <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                  <UserProfileLink
                    :user="request.user"
                    class="flex"
                  >
                    <div class="flex">
                      <img
                        :src="request.user.avatar"
                        class="size-10 rounded-full"
                      >
                      <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">
                          {{ request.user.name }}
                        </p>
                        <p class="text-sm text-gray-500">
                          {{ request.user.email }}
                        </p>
                      </div>
                    </div>
                  </UserProfileLink>
                </dd>
              </div>
              <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:py-5 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">
                  Rodzaj wniosku
                </dt>
                <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                  <VacationType :type="request.type" />
                </dd>
              </div>
              <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:py-5 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">
                  Obecny status
                </dt>
                <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                  <Status :status="request.state" />
                </dd>
              </div>
              <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:py-5 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">
                  Data
                </dt>
                <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                  <template v-if="request.days.length > 1">
                    {{ request.from }} - {{ request.to }}
                  </template>
                  <template v-else>
                    {{ request.from }}
                  </template>
                  <span class="font-semibold">
                    [Liczba dni: {{ request.days.length }}]
                  </span>
                </dd>
              </div>
              <div
                v-if="request.isVacation && (stats.limit > 0) && (request.can.acceptAsTechnical || request.can.acceptAsAdministrative || request.can.reject)"
                class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:py-5 sm:px-6"
              >
                <dt class="flex items-center text-sm font-medium text-gray-500">
                  Wykorzystanie urlopu
                </dt>
                <dd>
                  <VacationBar :stats="{ used: stats.used, pending: stats.pending, remaining: stats.remaining }" />
                </dd>
              </div>
              <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:py-5 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">
                  Komentarz
                </dt>
                <dd
                  v-if="request.comment != null"
                  class="mt-1 text-sm text-gray-900 break-all sm:col-span-2 sm:mt-0"
                >
                  {{ request.comment }}
                </dd>
                <dd
                  v-else
                  class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0"
                >
                  -
                </dd>
              </div>
              <div
                v-if="request.isVacation"
                class="py-5 px-4 bg-white sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6"
              >
                <dt class="flex items-center text-sm font-medium text-gray-500">
                  Załączniki
                </dt>
                <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                  <ul class="rounded-md border border-gray-200 divide-y divide-gray-200">
                    <li class="flex justify-between items-center py-3 pr-4 pl-3 text-sm">
                      <div class="flex flex-1 items-center w-0">
                        <PaperClipIcon class="shrink-0 size-5 text-gray-400" />
                        <span class="flex-1 ml-2 w-0 truncate">wniosek.pdf</span>
                      </div>
                      <div class="shrink-0 ml-4">
                        <a
                          :href="`/vacation/requests/${request.id}/download`"
                          class="font-medium text-blumilk-600 hover:text-blumilk-500"
                          target="_blank"
                        >
                          Pobierz
                        </a>
                      </div>
                    </li>
                  </ul>
                </dd>
              </div>
            </dl>
          </div>
        </div>
        <div
          v-if="request.can.acceptAsTechnical"
          class="bg-white shadow"
        >
          <div class="py-5 px-4 sm:p-6">
            <h3 class="text-lg font-medium leading-6 text-gray-900">
              Zaakceptuj wniosek jako osoba techniczna
            </h3>
            <div class="mt-2 max-w-xl text-sm text-gray-500">
              <p>
                W zależności od typu wniosku, zostanie on zatwierdzony lub osoba administracyjna będzie
                musiała go zaakceptować.
              </p>
            </div>
            <div class="mt-5">
              <InertiaLink
                :href="`/vacation/requests/${request.id}/accept-as-technical`"
                as="button"
                class="inline-flex justify-center py-2 px-4 text-sm font-medium text-white bg-blumilk-600 hover:bg-blumilk-700 rounded-md border border-transparent focus:outline-none focus:ring-2 focus:ring-blumilk-500 focus:ring-offset-2 shadow-sm"
                method="post"
                preserve-scroll
              >
                Zaakceptuj wniosek
              </InertiaLink>
            </div>
          </div>
        </div>
        <div
          v-if="request.can.acceptAsAdministrative"
          class="bg-white shadow"
        >
          <div class="py-5 px-4 sm:p-6">
            <h3 class="text-lg font-medium leading-6 text-gray-900">
              Zaakceptuj wniosek jako osoba administracyjna
            </h3>
            <div class="mt-2 max-w-xl text-sm text-gray-500">
              <p>
                Po akceptacji przez osobę administracyjną, wniosek zostanie zatwierdzony.
              </p>
            </div>
            <div class="mt-5">
              <InertiaLink
                :href="`/vacation/requests/${request.id}/accept-as-administrative`"
                as="button"
                class="inline-flex justify-center py-2 px-4 text-sm font-medium text-white bg-blumilk-600 hover:bg-blumilk-700 rounded-md border border-transparent focus:outline-none focus:ring-2 focus:ring-blumilk-500 focus:ring-offset-2 shadow-sm"
                method="post"
                preserve-scroll
              >
                Zaakceptuj wniosek
              </InertiaLink>
            </div>
          </div>
        </div>
        <div
          v-if="request.can.reject"
          class="bg-white shadow"
        >
          <div class="py-5 px-4 sm:p-6">
            <h3 class="text-lg font-medium leading-6 text-gray-900">
              Odrzuć wniosek
            </h3>
            <div class="mt-2 max-w-xl text-sm text-gray-500">
              <p>
                Odrzuconego wniosku nie można przywracać - należy zrobić nowy.
              </p>
            </div>
            <div class="mt-5">
              <InertiaLink
                :href="`/vacation/requests/${request.id}/reject`"
                as="button"
                class="inline-flex justify-center items-center py-2 px-4 font-medium text-red-700 bg-red-100 hover:bg-red-200 rounded-md border border-transparent focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 sm:text-sm"
                method="post"
                preserve-scroll
              >
                Odrzuć wniosek
              </InertiaLink>
            </div>
          </div>
        </div>
        <div
          v-if="request.can.cancel"
          class="bg-white border border-red-500 shadow"
        >
          <div class="py-5 px-4 sm:p-6">
            <h3 class="text-lg font-medium leading-6 text-gray-900">
              Anuluj wniosek
            </h3>
            <div class="mt-2 max-w-xl text-sm text-gray-500">
              <p>
                Anulowanego wniosku nie można przywrócić - należy utworzyć nowy.
              </p>
            </div>
            <div class="mt-5">
              <InertiaLink
                :href="`/vacation/requests/${request.id}/cancel`"
                as="button"
                class="inline-flex justify-center items-center py-2 px-4 font-medium text-red-700 bg-red-100 hover:bg-red-200 rounded-md border border-transparent focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 sm:text-sm"
                method="post"
                preserve-scroll
              >
                Anuluj wniosek
              </InertiaLink>
            </div>
          </div>
        </div>
      </div>
      <div class="space-y-6 xl:col-span-1 xl:col-start-3">
        <div class="bg-white shadow-md">
          <div class="py-5 px-4 sm:px-6">
            <h3 class="text-lg font-medium leading-6 text-gray-900">
              Historia wniosku
            </h3>
          </div>
          <div class="p-4 border-t border-gray-200">
            <ul>
              <li
                v-for="(activity, index) in activities.data"
                :key="index"
              >
                <Activity
                  :activity="activity"
                  :last="index !== activities.data.length - 1"
                />
              </li>
            </ul>
          </div>
        </div>
        <div
          v-if="(auth.can.manageRequestsAsAdministrativeApprover || auth.can.manageRequestsAsTechnicalApprover) && request.state !== 'cancelled'"
          class="bg-white shadow-md"
        >
          <div class="py-5 px-4 sm:px-6">
            <h3 class="text-lg font-medium leading-6 text-gray-900">
              Kalendarz pracownika
            </h3>
          </div>
          <div
            class="grid grid-cols-1 gap-8 py-8 px-4 mx-auto max-w-3xl border-t border-gray-200 sm:grid-cols-2 sm:px-6"
          >
            <CalendarComponent
              :key="handyCalendarKey"
              :end-month="handyCalendarData.endMonth"
              :holidays="handyCalendarData.holidays"
              :pending-vacations="handyCalendarData.pendingVacations"
              :start-month="handyCalendarData.startMonth"
              :vacations="handyCalendarData.vacations"
            />
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
