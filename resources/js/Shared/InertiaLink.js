import { h, ref } from 'vue'
import { Inertia, mergeDataIntoQueryString, shouldIntercept } from '@inertiajs/inertia'

export default {
  name: 'InertiaLink',
  props: {
    as: {
      type: String,
      default: 'a',
    },
    data: {
      type: Object,
      default: () => ({}),
    },
    href: {
      type: String,
    },
    method: {
      type: String,
      default: 'get',
    },
    replace: {
      type: Boolean,
      default: false,
    },
    preserveScroll: {
      type: Boolean,
      default: false,
    },
    preserveState: {
      type: Boolean,
      default: null,
    },
  },
  setup(props, { slots, attrs }) {
    const processing = ref(false)

    return props => {
      const as = props.as.toLowerCase()
      const method = props.method.toLowerCase()
      const [href, data] = mergeDataIntoQueryString(method, props.href || '', props.data, 'brackets')

      return h(props.as, {
        ...attrs,
        ...as === 'a' ? { href } : {},
        onClick: (event) => {
          if (shouldIntercept(event)) {
            event.preventDefault()

            Inertia.visit(href, {
              data: data,
              method: method,
              replace: props.replace,
              preserveScroll: props.preserveScroll,
              preserveState: props.preserveState ?? (method !== 'get'),
              onBefore: () => ! processing.value,
              onStart: () => processing.value = true,
              onFinish: () => processing.value = false,
            })
          }
        },
      }, slots)
    }
  },
}
