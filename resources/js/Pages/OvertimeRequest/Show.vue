<script setup>
import Activity from '@/Shared/Activity.vue'
import Status from '@/Shared/Status.vue'
import UserProfileLink from '@/Shared/UserProfileLink.vue'
import InertiaLink from '@/Shared/InertiaLink.vue'
import AppLayout from '@/Shared/Layout/AppLayout.vue'

defineProps({
  request: Object,
  activities: Object,
  stats: Object,
})

</script>

<template>
  <AppLayout :title="`Nadgodziny ${request.name}`">
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
                  {{ request.from }} - {{ request.to }}
                  <span class="font-semibold">
                    [Liczba godzin: {{ request.hours }}]
                  </span>
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
                :href="`/overtime/requests/${request.id}/accept-as-technical`"
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
                :href="`/overtime/requests/${request.id}/reject`"
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
                :href="`/overtime/requests/${request.id}/cancel`"
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
        <div
          v-if="request.can.settle"
          class="bg-white border border-blue-500 shadow"
        >
          <div class="py-5 px-4 sm:p-6">
            <h3 class="text-lg font-medium leading-6 text-gray-900">
              Rozlicz nadgodziny
            </h3>
            <div class="mt-2 max-w-xl text-sm text-gray-500">
              <p>
                Rozliczenie nadgodzin spowoduje oznaczenie ich jako rozliczone.
              </p>
            </div>
            <div class="mt-5">
              <InertiaLink
                :href="`/overtime/requests/${request.id}/settle`"
                as="button"
                class="inline-flex justify-center items-center py-2 px-4 font-medium text-blue-700 bg-blue-100 hover:bg-blue-200 rounded-md border border-transparent focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 sm:text-sm"
                method="post"
                preserve-scroll
              >
                Rozlicz nadgodziny
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
      </div>
    </div>
  </AppLayout>
</template>
