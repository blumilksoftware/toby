import {createApp, h} from 'vue';
import {createInertiaApp, Head, Link} from '@inertiajs/inertia-vue3';
import {InertiaProgress} from '@inertiajs/progress';
import AppLayout from '@/Shared/Layout/AppLayout';

createInertiaApp({
    resolve: name => {
        const page = require(`./Pages/${name}`).default;

        page.layout = page.layout || AppLayout;

        return page;
    },
    setup({el, App, props, plugin}) {
        createApp({render: () => h(App, props)})
            .use(plugin)
            .component('InertiaLink', Link)
            .component('InertiaHead', Head)
            .mount(el);
    },
    title: title => `${title} - Toby`,
});

InertiaProgress.init();
