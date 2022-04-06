import { computed } from 'vue'
import { usePage } from '@inertiajs/inertia-vue3'

export default function useCurrentYearPeriodInfo() {
  const minDate = computed(() => new Date(usePage().props.value.years.selected.year, 0, 1))
  const maxDate = computed(() => new Date(usePage().props.value.years.selected.year, 11, 31))
  const year = computed(() => usePage().props.value.years.selected.year)

  return {
    minDate,
    maxDate,
    year,
  }
}
