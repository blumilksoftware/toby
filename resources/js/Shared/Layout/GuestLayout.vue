<script setup>
import DeployInfo from '@/Shared/DeployInfo.vue'
import { Head } from '@inertiajs/vue3'
import { useGlobalProps } from '@/Composables/useGlobalProps'

const { deployInformation } = useGlobalProps()
</script>

<template>
  <Head>
    <slot name="title" />
  </Head>
  <div class="relative flex flex-col justify-center py-12 min-h-screen bg-blumilk-25 sm:px-6 lg:px-8">
    <slot />
    <DeployInfo
      :deploy-information="deployInformation"
      class="absolute left-0 bottom-0 justify-end"
    />
  </div>
</template>
