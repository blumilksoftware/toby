import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

export function useGlobalProps() {
  const page = usePage()

  return {
    auth: computed(() => page.props.auth),
    flash: computed(() => page.props.flash),
    errors: computed(() => page.props.errors),
    deployInformation: computed(() => page.props.deployInformation),
    vacationRequestsCount: computed(() => page.props.vacationRequestsCount),
    overtimeRequestsCount: computed(() => page.props.overtimeRequestsCount),
    lastUpdate: computed(() => page.props.lastUpdate),
  }
}
