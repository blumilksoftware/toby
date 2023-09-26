import '../css/app.css'
import { createApp, h } from 'vue'
import { createInertiaApp, Head } from '@inertiajs/inertia-vue3'
import { InertiaProgress } from '@inertiajs/progress'
import AppLayout from '@/Shared/Layout/AppLayout.vue'
import Flatpickr from 'flatpickr'
import { Settings } from 'luxon'
import { Polish } from 'flatpickr/dist/l10n/pl.js'
import Toast from 'vue-toastification'
import InertiaLink from '@/Shared/InertiaLink.js'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import FloatingVue from 'floating-vue'

createInertiaApp({
  resolve: (name) => {
    const page = resolvePageComponent(
      `./Pages/${name}.vue`,
      import.meta.glob('./Pages/**/*.vue'),
    )

    page.then((module) => {
      module.default.layout = module.default.layout || AppLayout
    })

    return page
  },
  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(Toast, {
        position: 'bottom-right',
        maxToast: 5,
        timeout: 3000,
        pauseOnFocusLoss: false,
      })
      .use(FloatingVue)
      .component('InertiaLink', InertiaLink)
      .component('InertiaHead', Head)
      .mount(el)
  },
  title: title => `${title} - Toby`,
})

InertiaProgress.init({
  delay: 0,
  color: '#527ABA',
})

Flatpickr.localize(Polish)
Flatpickr.setDefaults({
  dateFormat: 'Y-m-d',
  enableTime: false,
  altFormat: 'd.m.Y',
  altInput: true,
  disableMobile: true,
})

Settings.defaultLocale = 'pl'
