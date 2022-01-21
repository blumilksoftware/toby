<template>
    <InertiaHead title="Użytkownicy" />
    <div class="bg-white sm:rounded-lg shadow-md">
        <div class="flex justify-between items-center p-4 sm:px-6">
            <div>
                <h2 class="text-lg leading-6 font-medium text-gray-900">
                    Dostępne dni urlopu dla użytkowników
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Zarządzaj dostepnymi dniami urlopów dla użytkowników.
                </p>
            </div>
            <div class="ml-4">
                <span class="relative z-0 inline-flex shadow-sm rounded-md">
                    <InertiaLink
                        v-if="yearPeriods.prev"
                        :preserve-scroll="true"
                        replace
                        href="/vacation-days"
                        :data="{year: yearPeriods.prev.year}"
                        class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-blumilk-500 focus:border-blumilk-500"
                    >
                        <ChevronLeftIcon class="h-5 w-5" />
                    </InertiaLink>
                    <span
                        v-else
                        class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500"
                    >
                        <ChevronLeftIcon class="h-5 w-5" />
                    </span>
                    <span class="-ml-px relative inline-flex items-center px-2 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500">
                        {{ yearPeriods.current.year }}
                    </span>
                    <InertiaLink
                        v-if="yearPeriods.next"
                        :preserve-scroll="true"
                        href="/vacation-days"
                        replace
                        :data="{year: yearPeriods.next.year}"
                        class="-ml-px relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-blumilk-500 focus:border-blumilk-500"
                    >
                        <ChevronRightIcon class="h-5 w-5" />
                    </InertiaLink>
                    <span
                        v-else
                        class="-ml-px relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500"
                    >
                        <ChevronRightIcon class="h-5 w-5" />
                    </span>
                </span>
            </div>
        </div>
        <div class="border-t border-gray-200">
            <div class="overflow-x-auto xl:overflow-x-visible overflow-y-auto xl:overflow-y-visible">
                <form @submit.prevent="submitVacationDays">
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
                                    Forma zatrudnienia
                                </th>
                                <th
                                    scope="col"
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider"
                                >
                                    Posiada urlop?
                                </th>
                                <th
                                    scope="col"
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider"
                                >
                                    Dostępne dni w roku
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            <tr
                                v-for="item in form.items"
                                :key="item.id"
                                class="hover:bg-blumilk-25"
                            >
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div class="flex">
                                        <span
                                            class="inline-flex items-center justify-center h-10 w-10 rounded-full"
                                        >
                                            <img
                                                class="h-10 w-10 rounded-full"
                                                :src="item.user.avatar"
                                                alt=""
                                            >
                                        </span>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium break-all text-gray-900">
                                                {{ item.user.name }}
                                            </p>
                                            <p class="text-sm break-all text-gray-500">
                                                {{ item.user.email }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ item.user.employmentForm }}
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <Switch
                                        v-model="item.hasVacation"
                                        :class="[item.hasVacation ? 'bg-blumilk-500' : 'bg-gray-200', 'relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blumilk-500']"
                                    >
                                        <span
                                            :class="[item.hasVacation ? 'translate-x-5' : 'translate-x-0', 'pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200']"
                                        />
                                    </Switch>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div class="mt-1 sm:mt-0 sm:col-span-2">
                                        <input
                                            v-model="item.days"
                                            type="number"
                                            min="0"
                                            class="block w-full shadow-sm rounded-md sm:text-sm"
                                            :class="{ 'border-red-300 text-red-900 focus:outline-none focus:ring-red-500 focus:border-red-500': false, 'focus:ring-blumilk-500 focus:border-blumilk-500 sm:text-sm border-gray-300': true }"
                                        >
                                        <p
                                            v-if="false"
                                            class="mt-2 text-sm text-red-600"
                                        >
                                            {{ form.errors.name }}
                                        </p>
                                    </div>
                                </td>
                            </tr>
                            <tr
                                v-if="!form.items.length"
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
                    <div class="flex justify-end py-3 px-4">
                        <div class="space-x-3">
                            <InertiaLink
                                href="/users"
                                class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blumilk-500"
                            >
                                Anuluj
                            </InertiaLink>
                            <button
                                type="submit"
                                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blumilk-600 hover:bg-blumilk-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blumilk-500"
                            >
                                Zapisz
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import { Switch } from '@headlessui/vue';
import {ChevronLeftIcon, ChevronRightIcon} from '@heroicons/vue/solid';
import {useForm} from '@inertiajs/inertia-vue3';

export default {
    name: 'VacationDays',
    components: {
        Switch,
        ChevronLeftIcon,
        ChevronRightIcon,
    },
    props: {
        vacationLimits: {
            type: Object,
            default: () => null,
        },
        yearPeriods: {
            type: Object,
            default: () => null,
        },
    },
    setup(props) {
        const form = useForm({
            items: props.vacationLimits.data,
        });

        return {
            form,
        };
    },
    methods: {
        submitVacationDays() {
            this.form
                .transform(data => ({
                    data,
                }))
                .put('/vacation-days');
        },
    },
};
</script>
