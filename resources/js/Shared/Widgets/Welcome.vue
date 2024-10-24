<script setup>
defineProps({
  user: Object,
})
</script>

<template>
  <section>
    <div class=" overflow-hidden bg-white shadow">
      <div class="p-6 bg-white">
        <div class="sm:flex sm:justify-between sm:items-center">
          <div class="sm:flex sm:space-x-5">
            <div class="shrink-0">
              <img
                class="mx-auto size-20 rounded-full"
                :src="user.avatar"
              >
            </div>
            <div class="mt-4 text-center sm:pt-1 sm:mt-0 sm:text-left">
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
</template>
