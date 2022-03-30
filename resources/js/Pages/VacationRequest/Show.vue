<template>
  <InertiaHead :title="`Wniosek ${request.name}`" />
  <div class="grid grid-cols-1 gap-6 xl:grid-flow-col-dense xl:grid-cols-3">
    <div class="space-y-6 xl:col-start-1 xl:col-span-2">
      <div class="bg-white shadow-md">
        <div class="px-4 py-5 sm:px-6">
          <h3 class="text-lg leading-6 font-medium text-gray-900">
            Informacje na temat wniosku
          </h3>
        </div>
        <div class="border-t border-gray-200 px-4 py-5 sm:p-0">
          <dl class="sm:divide-y sm:divide-gray-200">
            <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-500">
                Nr wniosku
              </dt>
              <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                {{ request.name }}
              </dd>
            </div>
            <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-500 flex items-center">
                Pracownik
              </dt>
              <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                <div class="flex">
                  <img
                    class="h-10 w-10 rounded-full"
                    :src="request.user.avatar"
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
              </dd>
            </div>
            <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-500">
                Rodzaj urlopu
              </dt>
              <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                <VacationType :type="request.type" />
              </dd>
            </div>
            <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-500">
                Obecny status
              </dt>
              <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                <Status :status="request.state" />
              </dd>
            </div>
            <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-500">
                Data
              </dt>
              <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
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
            <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-500">
                Komentarz
              </dt>
              <dd
                v-if="request.comment != null"
                class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2"
              >
                {{ request.comment }}
              </dd>
              <dd
                v-else
                class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2"
              >
                -
              </dd>
            </div>
            <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-500 flex items-center">
                Załączniki
              </dt>
              <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                <ul class="border border-gray-200 rounded-md divide-y divide-gray-200">
                  <li class="pl-3 pr-4 py-3 flex items-center justify-between text-sm">
                    <div class="w-0 flex-1 flex items-center">
                      <PaperClipIcon class="flex-shrink-0 h-5 w-5 text-gray-400" />
                      <span class="ml-2 flex-1 w-0 truncate"> wniosek_urlopowy.pdf </span>
                    </div>
                    <div class="ml-4 flex-shrink-0">
                      <a
                        :href="`/vacation/requests/${request.id}/download`"
                        target="_blank"
                        class="font-medium text-blumilk-600 hover:text-blumilk-500"
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
        v-if="can.acceptAsTechnical"
        class="bg-white shadow"
      >
        <div class="px-4 py-5 sm:p-6">
          <h3 class="text-lg leading-6 font-medium text-gray-900">
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
              method="post"
              as="button"
              preserve-scroll
              class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blumilk-600 hover:bg-blumilk-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blumilk-500"
            >
              Zaakceptuj wniosek
            </InertiaLink>
          </div>
        </div>
      </div>
      <div
        v-if="can.acceptAsAdministrative"
        class="bg-white shadow"
      >
        <div class="px-4 py-5 sm:p-6">
          <h3 class="text-lg leading-6 font-medium text-gray-900">
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
              method="post"
              as="button"
              preserve-scroll
              class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blumilk-600 hover:bg-blumilk-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blumilk-500"
            >
              Zaakceptuj wniosek
            </InertiaLink>
          </div>
        </div>
      </div>
      <div
        v-if="can.reject"
        class="bg-white shadow"
      >
        <div class="px-4 py-5 sm:p-6">
          <h3 class="text-lg leading-6 font-medium text-gray-900">
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
              method="post"
              as="button"
              preserve-scroll
              class="inline-flex items-center justify-center px-4 py-2 border border-transparent font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:text-sm"
            >
              Odrzuć wniosek
            </InertiaLink>
          </div>
        </div>
      </div>
      <div
        v-if="can.cancel"
        class="bg-white shadow border border-red-500"
      >
        <div class="px-4 py-5 sm:p-6">
          <h3 class="text-lg leading-6 font-medium text-gray-900">
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
              method="post"
              as="button"
              preserve-scroll
              class="inline-flex items-center justify-center px-4 py-2 border border-transparent font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:text-sm"
            >
              Anuluj wniosek
            </InertiaLink>
          </div>
        </div>
      </div>
    </div>
    <div class="xl:col-start-3 xl:col-span-1 space-y-6">
      <div class="bg-white shadow-md">
        <div class="px-4 py-5 sm:px-6">
          <h3 class="text-lg leading-6 font-medium text-gray-900">
            Historia wniosku
          </h3>
        </div>
        <div class="border-t border-gray-200 px-4 py-4">
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
    </div>
  </div>
</template>

<script setup>
import { PaperClipIcon } from '@heroicons/vue/outline'
import Activity from '@/Shared/Activity'
import Status from '@/Shared/Status'
import VacationType from '@/Shared/VacationType'

defineProps({
  request: Object,
  can: Object,
  activities: Object,
})
</script>
