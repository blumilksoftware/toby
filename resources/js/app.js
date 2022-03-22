import { createApp, h } from 'vue'
import { createInertiaApp, Head, Link } from '@inertiajs/inertia-vue3'
import { InertiaProgress } from '@inertiajs/progress'
import AppLayout from '@/Shared/Layout/AppLayout'
import Flatpickr from 'flatpickr'
import { Polish } from 'flatpickr/dist/l10n/pl.js'
import Toast from 'vue-toastification'

createInertiaApp({
  resolve: name => {
    const page = require(`./Pages/${name}`).default

    page.layout = page.layout || AppLayout

    return page
  },
  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(Toast, {
        position: 'bottom-right',
        maxToast: 5,

      })
      .component('InertiaLink', Link)
      .component('InertiaHead', Head)
      .mount(el)
  },
  title: title => `${title} - Toby`,
})

InertiaProgress.init({
  delay: 0,
  color: 'red',
})

Flatpickr.localize(Polish)
Flatpickr.setDefaults({
  dateFormat: 'Y-m-d',
  enableTime: false,
  altFormat: 'd.m.Y',
  altInput: true,
})

