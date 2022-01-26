<template>
  <div class="grid grid-cols-1 gap-6 xl:grid-flow-col-dense xl:grid-cols-3">
    <div class="space-y-6 xl:col-start-1 xl:col-span-2">
      <div class="bg-white sm:rounded-lg shadow-md">
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
              <dt class="text-sm font-medium text-gray-500">
                Rodzaj urlopu
              </dt>
              <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                {{ request.type }}
              </dd>
            </div>
            <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-500">
                Urlop od
              </dt>
              <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                {{ request.from }}
              </dd>
            </div>
            <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-500">
                Urlop do
              </dt>
              <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                {{ request.to }}
              </dd>
            </div>
            <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-500">
                Dni
              </dt>
              <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                x
              </dd>
            </div>
            <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-500">
                Komentarz
              </dt>
              <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                {{ request.comment }}
              </dd>
            </div>
          </dl>
        </div>
      </div>
      <div class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
          <h3 class="text-lg leading-6 font-medium text-gray-900">
            Zaakceptuj wniosek jako osoba techniczna
          </h3>
          <div class="mt-2 max-w-xl text-sm text-gray-500">
            <p>
              W zależności od typu wniosku, zostanie on zatwierdzony lub osoba administracyjna będzie musiała go zaakceptować.
            </p>
          </div>
          <div class="mt-5">
            <InertiaLink
              :href="`/vacation-requests/${request.id}/accept-as-technical`"
              method="post"
              as="button"
              class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blumilk-600 hover:bg-blumilk-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blumilk-500"
            >
              Zaakceptuj wniosek
            </InertiaLink>
          </div>
        </div>
      </div>
      <div class="bg-white shadow sm:rounded-lg">
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
              :href="`/vacation-requests/${request.id}/accept-as-administrative`"
              method="post"
              as="button"
              class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blumilk-600 hover:bg-blumilk-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blumilk-500"
            >
              Zaakceptuj wniosek
            </InertiaLink>
          </div>
        </div>
      </div>
      <div class="bg-white shadow sm:rounded-lg">
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
              :href="`/vacation-requests/${request.id}/reject`"
              method="post"
              as="button"
              class="inline-flex items-center justify-center px-4 py-2 border border-transparent font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:text-sm"
            >
              Odrzuć wniosek
            </InertiaLink>
          </div>
        </div>
      </div>
      <div class="bg-white shadow sm:rounded-lg border border-red-500">
        <div class="px-4 py-5 sm:p-6">
          <h3 class="text-lg leading-6 font-medium text-gray-900">
            Anuluj wniosek
          </h3>
          <div class="mt-2 max-w-xl text-sm text-gray-500">
            <p>
              Wniosek można anulować w każdej chwili - nawet jeśli był już zatwierdzony.
            </p>
          </div>
          <div class="mt-5">
            <InertiaLink
              :href="`/vacation-requests/${request.id}/cancel`"
              method="post"
              as="button"
              class="inline-flex items-center justify-center px-4 py-2 border border-transparent font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:text-sm"
            >
              Anuluj wniosek
            </InertiaLink>
          </div>
        </div>
      </div>
    </div>
    <div class="xl:col-start-3 xl:col-span-1 space-y-6">
      <div class="bg-white sm:rounded-lg shadow-md">
        <div class="px-4 py-5 sm:px-6">
          <h3 class="text-lg leading-6 font-medium text-gray-900">
            Historia wniosku
          </h3>
        </div>
        <div class="border-t border-gray-200 px-4 py-4">
          <ul>
            <li
              v-for="(activity, index) in activities.data"
              :key="activity.id"
            >
              <div :class="{'relative pb-8': index !== activities.data.length - 1}">
                <span
                  v-if="(index !== activities.data.length - 1)"
                  class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200"
                />
                <div class="relative flex space-x-3">
                  <div>
                    <span class="bg-blumilk-500 h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white">
                      <ThumbUpIcon class="w-5 h-5 text-white" />
                    </span>
                  </div>
                  <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                    <div>
                      <p class="text-sm text-gray-500">
                        {{ activity.to }}
                      </p>
                    </div>
                    <div class="text-right text-sm whitespace-nowrap text-gray-500">
                      <time>{{ activity.date }}</time>
                    </div>
                  </div>
                </div>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ThumbUpIcon } from '@heroicons/vue/outline'

export default {
  name: 'VacationRequestShow',
  components: {
    ThumbUpIcon,
  },
  props: {
    request: {
      type: Object,
      default: () => null,
    },
    activities: {
      type: Object,
      default: () => null,
    },
  },
}
</script>

<style scoped>

</style>