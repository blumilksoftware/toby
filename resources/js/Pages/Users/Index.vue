<template>
    <div class="bg-white sm:rounded-lg shado-md">
        <div class="p-4 sm:px-6">
            <h2 class="text-lg leading-6 font-medium text-gray-900">
                Użytkownicy w organizacji
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Lista użytkowników w organizacji.
            </p>
        </div>
        <div class="border-t border-gray-200">
            <div class="overflow-x-auto xl:overflow-x-visible overflow-y-auto xl:overflow-y-visible">
                <div class="align-middle inline-block min-w-full">
                    <div class="shadow sm:rounded-b-lg">
                        <table
                            class="min-w-full divide-y divide-gray-200"
                        >
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
                                        Status
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
                                        Data dodania
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
                                    class="hover:bg-white"
                                >
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <div class="flex">
                                            <span class="inline-flex items-center justify-center h-10 w-10 rounded-full bg-indigo-500">
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
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800"
                                            data-cy="invited-status"
                                        >
                                            Zaproszony
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ user.role }}
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                        5 listopada 2021
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 text-right">
                                        <div
                                            x-data="dropdown"
                                            class="relative inline-block text-left"
                                            @keydown.escape="close()"
                                            @click.outside="close()"
                                        >
                                            <div>
                                                <button
                                                    class="rounded-full flex items-center text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 focus:ring-indigo-500"
                                                    data-cy="options-button"
                                                    @click="toggle()"
                                                >
                                                    <svg
                                                        class="h-5 w-5"
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        viewBox="0 0 20 20"
                                                        fill="currentColor"
                                                    >
                                                        <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                                    </svg>
                                                </button>
                                            </div>
                                            <div
                                                x-show="show"
                                                x-transition:enter="transition ease-out duration-100"
                                                x-transition:enter-start="transform opacity-0 scale-95"
                                                x-transition:enter-end="transform opacity-100 scale-100"
                                                x-transition:leave="transition ease-in duration-75"
                                                x-transition:leave-start="transform opacity-100 scale-100"
                                                x-transition:leave-end="transform opacity-0 scale-95"
                                                class="origin-top-right absolute right-6 mt-2 w-56 z-10 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 divide-y divide-gray-200"
                                                data-cy="options-list"
                                                style="display: none;"
                                            >
                                                <a
                                                    href="#"
                                                    role="button"
                                                    class="group flex items-center px-4 py-2 text-sm font-semibold text-gray-500 hover:bg-gray-100"
                                                    wire:click.prevent="inviteAgain"
                                                    data-cy="invite-again-button"
                                                    @click="close()"
                                                >
                                                    Wyślij zaproszenie ponownie
                                                </a>
                                                <a
                                                    href="#"
                                                    role="button"
                                                    class="group flex items-center px-4 py-2 text-sm font-semibold text-gray-500 hover:bg-gray-100"
                                                    wire:click.prevent="cancelInvitation"
                                                    data-cy="cancel-invitation-button"
                                                    @click="close()"
                                                >
                                                    <span class="text-red-500">
                                                        Anuluj zaproszenie
                                                    </span>
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'UserIndex',
    props: {
        users: {
            type: Object,
            default: () => {},
        }
    }
};
</script>
