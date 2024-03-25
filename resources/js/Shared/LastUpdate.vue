<script setup>
import { onMounted } from 'vue'
import axios from 'axios'
import { updateFavicon } from '@/Shared/updateFavicon'

const props = defineProps({
  lastUpdate: String,
})

const emit = defineEmits(['lastUpdateUpdated'])

const fetchLastUpdate = async () => {
  try {
    const response = await axios.get('/api/last-update')
    if (response.data.lastUpdate !== props.lastUpdate) {
      emit('lastUpdateUpdated')
      updateFavicon('/images/icon-alert.png')
    }
  } catch (error) {
    console.error('Failed to fetch last update.')
  }
}

onMounted(() => {
  fetchLastUpdate()
  setInterval(fetchLastUpdate, 300000) // 5 minutes
})
</script>
