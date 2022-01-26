<template>
    <Popover
        v-slot="{ open }"
        as="header"
        class="pb-24 bg-gradient-to-r from-blumilk-500 to-blumilk-600"
    >
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:max-w-7xl lg:px-8">
            <div class="relative flex flex-wrap items-center justify-center lg:justify-between">
                <!-- Logo -->
                <div class="absolute left-0 py-5 flex-shrink-0 lg:static">
                    <InertiaLink href="/">
                        <img
                            class="h-8 w-auto"
                            src="/img/logo-white.png"
                            alt="Workflow"
                        >
                    </InertiaLink>
                </div>

                <!-- Right section on desktop -->
                <div class="hidden lg:ml-4 lg:flex lg:items-center lg:py-5 lg:pr-0.5">
                    <div class="mr-4">
                        <Menu
                            as="div"
                            class="relative inline-block text-left"
                        >
                            <div>
                                <MenuButton class="inline-flex justify-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-1 focus:ring-gray-300">
                                    {{ years.current }}
                                    <ChevronDownIcon class="-mr-1 ml-2 h-5 w-5" />
                                </MenuButton>
                            </div>

                            <transition
                                enter-active-class="transition ease-out duration-100"
                                enter-from-class="transform opacity-0 scale-95"
                                enter-to-class="transform opacity-100 scale-100"
                                leave-active-class="transition ease-in duration-75"
                                leave-from-class="transform opacity-100 scale-100"
                                leave-to-class="transform opacity-0 scale-95"
                            >
                                <MenuItems class="origin-top-right absolute right-0 mt-2 w-32 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none">
                                    <div class="py-1">
                                        <MenuItem
                                            v-for="(item, index) in years.navigation"
                                            :key="index"
                                            v-slot="{ active }"
                                        >
                                            <InertiaLink
                                                :href="item.link"
                                                as="button"
                                                method="post"
                                                :preserve-state="false"
                                                :class="[active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'flex w-full px-4 py-2 text-sm']"
                                            >
                                                {{ item.year }}
                                                <CheckIcon
                                                    v-if="item.year === years.current"
                                                    class="h-5 w-5 text-blumilk-500 ml-2"
                                                />
                                            </InertiaLink>
                                        </MenuItem>
                                    </div>
                                </MenuItems>
                            </transition>
                        </Menu>
                    </div>

                    <button
                        type="button"
                        class="flex-shrink-0 p-1 text-cyan-200 rounded-full hover:text-white hover:bg-white hover:bg-opacity-10 focus:outline-none focus:ring-2 focus:ring-white"
                    >
                        <span class="sr-only">View notifications</span>
                        <BellIcon
                            class="h-6 w-6"
                            aria-hidden="true"
                        />
                    </button>

                    <!-- Profile dropdown -->
                    <Menu
                        as="div"
                        class="ml-4 relative flex-shrink-0"
                    >
                        <div>
                            <MenuButton
                                class="rounded-full flex text-sm ring-2 ring-white ring-opacity-20 focus:outline-none focus:ring-opacity-100"
                                dusk="user-menu"
                            >
                                <span class="sr-only">{{ user.avatar }}</span>
                                <img
                                    class="h-8 w-8 rounded-full"
                                    :src="user.avatar"
                                    alt=""
                                >
                            </MenuButton>
                        </div>
                        <transition
                            leave-active-class="transition ease-in duration-75"
                            leave-from-class="transform opacity-100 scale-100"
                            leave-to-class="transform opacity-0 scale-95"
                        >
                            <MenuItems
                                class="origin-top-right z-40 absolute -right-2 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none"
                                dusk="user-menu-list"
                            >
                                <MenuItem
                                    v-for="item in userNavigation"
                                    :key="item.name"
                                    v-slot="{ active }"
                                >
                                    <InertiaLink
                                        :href="item.href"
                                        :method="item.method"
                                        :as="item.as"
                                        :class="[active ? 'bg-gray-100' : '', 'block w-full text-left px-4 py-2 text-sm text-gray-700']"
                                    >
                                        {{ item.name }}
                                    </InertiaLink>
                                </MenuItem>
                            </MenuItems>
                        </transition>
                    </Menu>
                </div>

                <div class="w-full py-5 lg:border-t lg:border-white lg:border-opacity-20">
                    <div class="lg:items-center">
                        <div class="hidden lg:block">
                            <nav class="flex space-x-4">
                                <InertiaLink
                                    v-for="item in navigation"
                                    :key="item.name"
                                    :href="item.href"
                                    :class="[item.current ? 'text-white' : 'text-cyan-100', 'text-sm font-medium rounded-md bg-white bg-opacity-0 px-3 py-2 hover:bg-opacity-10']"
                                    :aria-current="item.current ? 'page' : undefined"
                                >
                                    {{ item.name }}
                                </InertiaLink>
                            </nav>
                        </div>
                    </div>
                </div>

                <!-- Menu button -->
                <div class="absolute right-0 flex-shrink-0 lg:hidden">
                    <!-- Mobile menu button -->
                    <PopoverButton
                        class="bg-transparent p-2 rounded-md inline-flex items-center justify-center text-cyan-200 hover:text-white hover:bg-white hover:bg-opacity-10 focus:outline-none focus:ring-2 focus:ring-white"
                    >
                        <span class="sr-only">Open main menu</span>
                        <MenuIcon
                            v-if="!open"
                            class="block h-6 w-6"
                            aria-hidden="true"
                        />
                        <XIcon
                            v-else
                            class="block h-6 w-6"
                            aria-hidden="true"
                        />
                    </PopoverButton>
                </div>
            </div>
        </div>

        <TransitionRoot
            as="template"
            :show="open"
        >
            <div class="lg:hidden">
                <TransitionChild
                    as="template"
                    enter="duration-150 ease-out"
                    enter-from="opacity-0"
                    enter-to="opacity-100"
                    leave="duration-150 ease-in"
                    leave-from="opacity-100"
                    leave-to="opacity-0"
                >
                    <PopoverOverlay class="z-20 fixed inset-0 bg-black bg-opacity-25" />
                </TransitionChild>

                <TransitionChild
                    as="template"
                    enter="duration-150 ease-out"
                    enter-from="opacity-0 scale-95"
                    enter-to="opacity-100 scale-100"
                    leave="duration-150 ease-in"
                    leave-from="opacity-100 scale-100"
                    leave-to="opacity-0 scale-95"
                >
                    <PopoverPanel
                        focus
                        class="z-30 absolute top-0 inset-x-0 max-w-3xl mx-auto w-full p-2 transition transform origin-top"
                    >
                        <div
                            class="rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 bg-white divide-y divide-gray-200"
                        >
                            <div class="pt-3 pb-2">
                                <div class="flex items-center justify-between px-4">
                                    <div>
                                        <img
                                            class="h-8 w-auto"
                                            src="/img/logo-white.png"
                                            alt="Workflow"
                                        >
                                    </div>
                                    <div class="-mr-2">
                                        <PopoverButton
                                            class="bg-white rounded-md p-2 inline-flex items-center justify-center text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-cyan-500"
                                        >
                                            <span class="sr-only">Close menu</span>
                                            <XIcon
                                                class="h-6 w-6"
                                                aria-hidden="true"
                                            />
                                        </PopoverButton>
                                    </div>
                                </div>
                                <div class="mt-3 px-2 space-y-1">
                                    <InertiaLink
                                        v-for="item in navigation"
                                        :key="item.name"
                                        :href="item.href"
                                        class="block rounded-md px-3 py-2 text-base text-gray-900 font-medium hover:bg-gray-100 hover:text-gray-800"
                                    >
                                        {{ item.name }}
                                    </InertiaLink>
                                </div>
                            </div>
                            <div class="pt-4 pb-2">
                                <div class="flex items-center px-5">
                                    <div class="flex-shrink-0">
                                        <img
                                            class="h-10 w-10 rounded-full"
                                            :src="user.avatar"
                                            alt=""
                                        >
                                    </div>
                                    <div class="ml-3 min-w-0 flex-1">
                                        <div class="text-base font-medium text-gray-800 truncate">
                                            {{ user.name }}
                                        </div>
                                        <div class="text-sm font-medium text-gray-500 truncate">
                                            {{ user.email }}
                                        </div>
                                    </div>
                                    <button
                                        type="button"
                                        class="ml-auto flex-shrink-0 bg-white p-1 text-gray-400 rounded-full hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500"
                                    >
                                        <span class="sr-only">View notifications</span>
                                        <BellIcon
                                            class="h-6 w-6"
                                            aria-hidden="true"
                                        />
                                    </button>
                                </div>
                                <div class="mt-3 px-2 space-y-1">
                                    <InertiaLink
                                        v-for="item in userNavigation"
                                        :key="item.name"
                                        :method="item.method"
                                        :as="item.as"
                                        :href="item.href"
                                        class="block w-full text-left rounded-md px-3 py-2 text-base text-gray-900 font-medium hover:bg-gray-100 hover:text-gray-800"
                                    >
                                        {{ item.name }}
                                    </InertiaLink>
                                </div>
                            </div>
                        </div>
                    </PopoverPanel>
                </TransitionChild>
            </div>
        </TransitionRoot>
    </Popover>
</template>

<script>
import {
    Menu,
    MenuButton,
    MenuItem,
    MenuItems,
    Popover,
    PopoverButton,
    PopoverOverlay,
    PopoverPanel,
    TransitionChild,
    TransitionRoot,
} from '@headlessui/vue';
import {BellIcon, MenuIcon, XIcon} from '@heroicons/vue/outline';
import {computed} from 'vue';
import {usePage} from '@inertiajs/inertia-vue3';
import {ChevronDownIcon, CheckIcon} from '@heroicons/vue/solid';

export default {
    name: 'MainMenu',
    components: {
        Menu,
        MenuButton,
        MenuItem,
        MenuItems,
        Popover,
        PopoverButton,
        PopoverOverlay,
        PopoverPanel,
        TransitionChild,
        TransitionRoot,
        BellIcon,
        MenuIcon,
        XIcon,
        ChevronDownIcon,
        CheckIcon,
    },
    setup() {
        const user = computed(() => usePage().props.value.auth.user);
        const years = computed(() => usePage().props.value.years);

        const navigation = [
            {name: 'Strona główna', href: '/', current: true},
            {name: 'Użytkownicy', href: '/users', current: false},
            {name: 'Dostępne urlopy', href: '/vacation-limits', current: false},
            {name: 'Dni wolne', href: '/holidays', current: false},
            {name: 'Wnioski urlopowe', href: '/vacation-requests', current: false},
        ];
        const userNavigation = [
            {name: 'Your Profile', href: '#'},
            {name: 'Settings', href: '#'},
            {name: 'Wyloguj się', href: '/logout', method: 'post', as: 'button'},
        ];

        return {
            user,
            years,
            navigation,
            userNavigation,
        };
    },
};

</script>

