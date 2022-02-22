import {computed} from 'vue'
import {usePage} from '@inertiajs/inertia-vue3'

export default function useCurrentYearPeriodInfo() {
  const minDate = computed(() => new Date(usePage().props.value.years.current, 0, 1))
  const maxDate = computed(() => new Date(usePage().props.value.years.current, 11, 31))

  return {
    minDate,
    maxDate,
  }
}