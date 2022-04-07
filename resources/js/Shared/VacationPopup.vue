<template>
  <div class="py-2 px-6 text-left text-gray-900 whitespace-nowrap bg-white rounded-lg border border-gray-400">
    <dl class="divide-y divide-gray-200">
      <div class="py-2 space-y-1">
        <dt class="text-sm font-medium text-gray-500">
          Nr wniosku
        </dt>
        <dd class="text-sm text-gray-900">
          <InertiaLink
            :href="`/vacation/requests/${vacation.id}`"
            class="font-semibold text-blumilk-600 hover:text-blumilk-500 hover:underline"
          >
            {{ vacation.name }}
          </InertiaLink>
        </dd>
      </div>
      <div class="py-2 space-y-1">
        <dt class="text-sm font-medium text-gray-500">
          Rodzaj urlopu
        </dt>
        <dd class="mt-1 text-sm text-gray-900">
          <VacationType :type="vacation.type" />
        </dd>
      </div>
      <div class="py-2 space-y-1">
        <dt class="text-sm font-medium text-gray-500">
          Obecny status
        </dt>
        <dd class="mt-1 text-sm text-gray-900">
          <Status :status="vacation.state" />
        </dd>
      </div>
      <div class="py-2 space-y-1">
        <dt class="text-sm font-medium text-gray-500">
          Data
        </dt>
        <dd class="mt-1 text-sm text-gray-900">
          <template v-if="vacation.days > 1">
            {{ vacation.from }} - {{ vacation.to }}
          </template>
          <template v-else>
            {{ vacation.from }}
          </template>
          <span class="font-semibold">
            [Liczba dni: {{ vacation.days }}]
          </span>
        </dd>
      </div>
    </dl>
  </div>
</template>


<script setup>
import VacationType from '@/Shared/VacationType'
import Status from '@/Shared/Status'

defineProps({
  vacation: Object,
})

</script>
