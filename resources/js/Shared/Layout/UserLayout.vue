<script setup>

import InertiaLink from '@/Shared/InertiaLink.vue'
import AppLayout from '@/Shared/Layout/AppLayout.vue'
import { computed } from 'vue'

const props = defineProps({
  user: Object,
  title: String,
})

const pages = computed(() => [
  {
    name: 'Informacje podstawowe',
    href: `/users/${props.user.id}`,
  },
  {
    name: 'Edycja danych',
    href: `/users/${props.user.id}/edit`,
  },
  {
    name: 'Uprawnienia',
    href: `/users/${props.user.id}/permissions`,
  },
  {
    name: 'Historia',
    href: `/users/${props.user.id}/history`,
  },
  {
    name: 'Wnioski',
    href: `/vacation/requests?user=${props.user.id}`,
  },
])
</script>

<template>
  <AppLayout :title="title">
    <section>
      <div class="bg-white shadow mb-8">
        <div class="p-6 bg-white">
          <div class="sm:flex sm:justify-between sm:items-center">
            <div class="sm:flex sm:space-x-5 sm:items-center">
              <div class="shrink-0">
                <img
                  :src="user.avatar"
                  class="mx-auto size-20 rounded-full"
                >
              </div>
              <div class="mt-4 text-center sm:pt-1 sm:mt-0 sm:text-left">
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
        <div class="border-t relative divide-x divide-gray-200 shadow flex flex-col md:flex-row">
          <InertiaLink
            v-for="page in pages"
            :key="page.href"
            :href="page.href"
            :class="[$page.url === page.href ? 'text-blumilk-600 font-semibold' : 'hover:bg-blumilk-25 text-gray-700 focus:z-10', 'group relative min-w-0 flex-1 overflow-hidden focus:outline-blumilk-500 bg-white p-4 text-sm font-medium text-center']"
          >
            <span>{{ page.name }}</span>
            <span
              :class="[$page.url === page.href ? 'bg-blumilk-500' : 'bg-transparent', 'absolute inset-x-0 bottom-0 h-0.5']"
            />
          </InertiaLink>
        </div>
      </div>
    </section>
    <slot />
  </AppLayout>
</template>
