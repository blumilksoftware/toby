<template>
    <InertiaHead title="Kalendarz" />
    <div class="bg-white sm:rounded-lg shadow-md">
        <div class="p-4 sm:px-6">
            <h2 class="text-lg leading-6 font-medium text-gray-900">
                Kalendarz
            </h2>
        </div>
        <div class="grid grid-cols-38 justify-items-center divide-x divide-gray-300">
            <div class="w-full text-center col-span-7">
                <Menu as="div" class="relative inline-block text-left">
                    <div>
                        <MenuButton class="inline-flex justify-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 focus:ring-blumilk-500">
                            Styczeń
                            <ChevronDownIcon class="-mr-1 ml-2 h-5 w-5" aria-hidden="true" />
                        </MenuButton>
                    </div>

                    <transition enter-active-class="transition ease-out duration-100" enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100" leave-active-class="transition ease-in duration-75" leave-from-class="transform opacity-100 scale-100" leave-to-class="transform opacity-0 scale-95">
                        <MenuItems class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none">
                            <div class="py-1">
                                <MenuItem>
                                    <a href="#" class="text-gray-900 block px-4 py-2 text-sm">Styczeń</a>
                                </MenuItem>
                                <MenuItem>
                                    <a href="#" class="text-gray-900 block px-4 py-2 text-sm">Luty</a>
                                </MenuItem>
                                <MenuItem>
                                    <a href="#" class="text-gray-900 block px-4 py-2 text-sm">Marzec</a>
                                </MenuItem>
                            </div>
                        </MenuItems>
                    </transition>
                </Menu>
            </div>
            <div class="w-full text-center font-semibold text-gray-900 py-4 bg-gray-50" v-for="n in 31">{{ n }}
                <p class="text-gray-800 font-normal mt-1">Pt</p>
            </div>
        </div>
        <div class="grid grid-cols-38 justify-items-start divide-x divide-gray-300 border-t border-gray-300" v-for="user in users.data"
             :key="user.id">
            <div class="col-span-7 px-4 py-2 whitespace-nowrap">
                <div class="flex justify-start items-center">
                      <span
                          class="inline-flex items-center justify-center h-10 w-10 rounded-full"
                      >
                                        <img
                                            class="h-10 w-10 rounded-full"
                                            :src="user.avatar"
                                            alt=""
                                        >
                                    </span>
                    <div class="ml-3">
                        <div class="text-sm font-medium break-all text-gray-900">
                            {{ user.name }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="w-full bg-blumilk-25 divide-x divide-gray-300 flex justify-center" v-for="n in 31">
<!--                <div class="bg-blumilk-500 items-center w-96 m-1 rounded-lg"></div>-->
            </div>
        </div>
    </div>
</template>

<script>
import FlatPickr from 'vue-flatpickr-component';
import {Listbox, ListboxButton, ListboxLabel, ListboxOption, ListboxOptions} from '@headlessui/vue';
import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue'
import {CheckIcon, SelectorIcon} from '@heroicons/vue/solid';
import { ChevronDownIcon } from '@heroicons/vue/solid'

export default {
    components: {
        FlatPickr,
        Listbox,
        ListboxButton,
        ListboxLabel,
        ListboxOption,
        ListboxOptions,
        Menu,
        MenuButton,
        MenuItem,
        MenuItems,
        CheckIcon,
        SelectorIcon,
        ChevronDownIcon,
    },
    props: {
        users: {
            type: Object,
            default: () => null,
        },
    },
};
</script>
