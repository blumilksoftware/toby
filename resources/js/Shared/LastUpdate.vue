<script setup>
import { onMounted, onUnmounted } from 'vue'
import axios from 'axios'

const emit = defineEmits(['lastUpdateUpdated'])

let interval = undefined

const fetchLastUpdate = async () => {
  try {
    const response = await axios.get('/api/last-update')
    emit('lastUpdateUpdated', response.data.lastUpdate)
  } catch (error) {
    console.error('Failed to fetch last update.')
  }
}

onMounted(() => {
  interval = setInterval(fetchLastUpdate, import.meta.env.VITE_LAST_UPDATE_TIMEOUT)
})

onUnmounted(() => clearInterval(interval))
</script>

<template>
  <span hidden />
</template>
