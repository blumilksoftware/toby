<template>
    <InertiaHead title="Użytkownicy" />
    <div class="bg-white sm:rounded-lg shadow-md">
        <div class="p-4 sm:px-6">
            <h2 class="text-lg leading-6 font-medium text-gray-900">
                Użytkownicy w organizacji
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Lista użytkowników w organizacji.
            </p>
        </div>
        <div class="border-t border-gray-200">
            <div class="px-4 py-3">
                <div class="relative max-w-md">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <SearchIcon class="h-5 w-5 text-gray-400" />
                    </div>
                    <input
                        v-model.trim="search"
                        type="search"
                        class="block w-full bg-white border border-gray-300 rounded-md py-2 pl-10 pr-3 text-sm placeholder-gray-500 focus:outline-none focus:text-gray-900 focus:placeholder-gray-400 focus:ring-1 focus:ring-blumilk-500 focus:border-blumilk-500 sm:text-sm mt-1"
                        placeholder="Szukaj"
                    >
                </div>
            </div>
            <div class="overflow-x-auto xl:overflow-x-visible overflow-y-auto xl:overflow-y-visible">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th
                                scope="col"
                                class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider"
                            >
                                Imię i nazwisko
                            </th>
                            <th
                                scope="col"
                                class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider"
                            >
                                Rola
                            </th>
                            <th
                                scope="col"
                                class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider"
                            >
                                Forma zatrudnienia
                            </th>
                            <th
                                scope="col"
                                class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider"
                            >
                                Data rozpoczęcia
                            </th>
                            <th
                                scope="col"
                                class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider"
                            />
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        <tr
                            v-for="user in users.data"
                            :key="user.id"
                            class="hover:bg-blumilk-25"
                        >
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                <div class="flex">
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
                                        <p class="text-sm font-medium break-all text-gray-900">
                                            {{ user.name }}
                                        </p>
                                        <p class="text-sm break-all text-gray-500">
                                            {{ user.email }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ user.role }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ user.employmentForm }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ user.employmentStartDate }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 text-right">
                                <div>
                                    <button
                                        class="rounded-full flex items-center text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 focus:ring-blumilk-500"
                                    >
                                        <svg
                                            class="h-5 w-5"
                                            xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20"
                                            fill="currentColor"
                                        >
                                            <path
                                                d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"
                                            />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr
                            v-if="! users.data.length"
                        >
                            <td
                                colspan="100%"
                                class="text-center py-4 text-xl leading-5 text-gray-700"
                            >
                                Brak danych
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div
                    v-if="users.data.length"
                    class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6 rounded-b-lg"
                >
                    <div class="flex-1 flex justify-between sm:hidden">
                        <InertiaLink
                            :is="users.links.prev ? 'InertiaLink': 'span'"
                            :href="users.links.prev"
                            class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                        >
                            Poprzednia
                        </InertiaLink>
                        <Component
                            :is="users.links.next ? 'InertiaLink': 'span'"
                            :href="users.links.next"
                            class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                        >
                            Następna
                        </Component>
                    </div>
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div class="text-sm text-gray-700">
                            Wyświetlanie
                            <span class="font-medium">{{ users.meta.from }}</span>
                            od
                            <span class="font-medium">{{ users.meta.to }}</span>
                            do
                            <span class="font-medium">{{ users.meta.total }}</span>
                            wyników
                        </div>
                        <nav class="relative z-0 inline-flex space-x-1">
                            <template
                                v-for="(link, index) in users.meta.links"
                                :key="index"
                            >
                                <Component
                                    :is="link.url ? 'InertiaLink' : 'span'"
                                    :href="link.url"
                                    class="relative inline-flex items-center px-4 py-2 border rounded-md text-sm font-medium"
                                    :class="{ 'z-10 bg-blumilk-25 border-blumilk-500 text-blumilk-600': link.active, 'bg-white border-gray-300 text-gray-500': !link.active, 'hover:bg-blumilk-25': link.url, 'border-none': !link.url}"
                                    v-text="link.label"
                                />
                            </template>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { ref, watch } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import { debounce } from 'lodash';
import { SearchIcon } from '@heroicons/vue/outline';

export default {
    name: 'UserIndex',
    components: {
        SearchIcon,
    },
    props: {
        users: {
            type: Object,
            default: () => null,
        },
        filters: {
            type: Object,
            default: () => null,
        },
    },
    setup(props) {
        let search = ref(props.filters.search);

        watch(search, debounce(value => {
            Inertia.get('/users', value ? { search: value} : {}, {
                preserveState: true,
                replace: true,
            });
        }, 300));

        return {
            search,
        };
    }
};
</script>
