<template>
    <div
        class="sm:mx-auto sm:w-full sm:max-w-md text-white space-y-4 flex flex-col items-center rounded-lg px-4 py-8"
    >
        <img
            class="mx-auto h-12 w-auto"
            src="img/logo.png"
            alt="Blumilk"
        >
        <a
            href="/login/google/start"
            class="inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
        >
            Zaloguj się poprzez konto Google
            <svg
                class="w-5 h-5 ml-2"
                fill="currentColor"
                viewBox="0 0 24 24"
            >
                <path
                    d="M6 12C6 15.3137 8.68629 18 12 18C14.6124 18 16.8349 16.3304 17.6586 14H12V10H21.8047V14H21.8C20.8734 18.5645 16.8379 22 12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2C15.445 2 18.4831 3.742 20.2815 6.39318L17.0039 8.68815C15.9296 7.06812 14.0895 6 12 6C8.68629 6 6 8.68629 6 12Z"
                    fill="currentColor"
                />
            </svg>
        </a>
        <div
            v-if="errors.oauth"
            class="w-full rounded-md bg-red-100 border border-red-400 p-4"
        >
            <div class="flex">
                <div class="flex-shrink-0">
                    <XCircleIcon class="h-5 w-5 text-red-400" />
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">
                        {{ errors.oauth }}
                    </h3>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import {XCircleIcon} from '@heroicons/vue/solid';
import GuestLayout from '@/Shared/Layout/GuestLayout';

export default {
    name: 'Login',
    components: {
        XCircleIcon,
    },
    layout: GuestLayout,
    props: {
        errors: {
            type: Object,
            default: () => ({oauth: null}),
        }
    },
};
</script>