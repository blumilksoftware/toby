import '../css/app.css'
import { createApp, h } from 'vue'
import Flatpickr from 'flatpickr'
import { Settings } from 'luxon'
import Toast from 'vue-toastification'
import FloatingVue from 'floating-vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { Polish } from 'flatpickr/dist/l10n/pl'

const appName = import.meta.env.VITE_APP_NAME || 'Toby'

createInertiaApp({
  title: (title) => title ? `${title} - ${appName}` : appName,
  resolve: name => {
    const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
    return pages[`./Pages/${name}.vue`]
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
      .mount(el)
  },
  progress: {
    delay: 0,
    color: '#527ABA',
  },
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
