<template>
    <InertiaHead title="Dodawanie użytkownika" />
    <div class="bg-white sm:rounded-lg shadow-md">
        <div class="p-4 sm:px-6">
            <h2 class="text-lg leading-6 font-medium text-gray-900">
                Dodaj użytkownika
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Tylko dodani użytkownicy będą mogli zalogować się do aplikacji.
            </p>
        </div>
        <form
            class="border-t border-gray-200 px-6"
            @submit.prevent="form.post('/users')"
        >
            <div class="sm:grid sm:grid-cols-3 py-4 items-center">
                <label
                    for="name"
                    class="block text-sm font-medium text-gray-700 sm:mt-px"
                >
                    Imię i nazwisko
                </label>
                <div class="mt-1 sm:mt-0 sm:col-span-2">
                    <input
                        id="name"
                        v-model="form.name"
                        type="text"
                        class="block w-full max-w-lg shadow-sm rounded-md sm:text-sm"
                        :class="{ 'border-red-300 text-red-900 focus:outline-none focus:ring-red-500 focus:border-red-500': form.errors.name, 'focus:ring-blumilk-500 focus:border-blumilk-500 sm:text-sm border-gray-300': !form.errors.name }"
                    >
                    <p
                        v-if="form.errors.name"
                        class="mt-2 text-sm text-red-600"
                    >
                        {{ form.errors.name }}
                    </p>
                </div>
            </div>
            <div class="sm:grid sm:grid-cols-3 py-4 items-center">
                <label
                    for="email"
                    class="block text-sm font-medium text-gray-700 sm:mt-px"
                >
                    Adres email
                </label>
                <div class="mt-1 sm:mt-0 sm:col-span-2">
                    <input
                        id="email"
                        v-model="form.email"
                        type="email"
                        class="block w-full max-w-lg shadow-sm rounded-md sm:text-sm"
                        :class="{ 'border-red-300 text-red-900 focus:outline-none focus:ring-red-500 focus:border-red-500': form.errors.email, 'focus:ring-blumilk-500 focus:border-blumilk-500 sm:text-sm border-gray-300': !form.errors.email }"
                    >
                    <p
                        v-if="form.errors.email"
                        class="mt-2 text-sm text-red-600"
                    >
                        {{ form.errors.email }}
                    </p>
                </div>
            </div>
            <div class="sm:grid sm:grid-cols-3 py-4 items-center">
                <label
                    for="employment_form"
                    class="block text-sm font-medium text-gray-700 sm:mt-px"
                >
                    Forma zatrudnienia
                </label>
                <div class="mt-1 sm:mt-0 sm:col-span-2">
                    <select
                        id="employment_form"
                        v-model="form.employmentForm"
                        class="block w-full max-w-lg shadow-sm rounded-md sm:text-sm"
                        :class="{ 'border-red-300 text-red-900 focus:outline-none focus:ring-red-500 focus:border-red-500': form.errors.employmentForm, 'focus:ring-blumilk-500 focus:border-blumilk-500 sm:text-sm border-gray-300': !form.errors.employmentForm }"
                    >
                        <option
                            v-for="employmentForm in employmentForms"
                            :key="employmentForm.value"
                            :value="employmentForm.value"
                        >
                            {{ employmentForm.label }}
                        </option>
                    </select>
                    <p
                        v-if="form.errors.employmentForm"
                        class="mt-2 text-sm text-red-600"
                    >
                        {{ form.errors.employmentForm }}
                    </p>
                </div>
            </div>
            <div class="sm:grid sm:grid-cols-3 py-4 items-center">
                <label
                    for="employment_date"
                    class="block text-sm font-medium text-gray-700 sm:mt-px"
                >
                    Data zatrudnienia
                </label>
                <div class="mt-1 sm:mt-0 sm:col-span-2">
                    <FlatPickr
                        id="employment_date"
                        v-model="form.employmentDate"
                        placeholder="Wybierz datę"
                        class="block w-full max-w-lg shadow-sm rounded-md sm:text-sm"
                        :class="{ 'border-red-300 text-red-900 focus:outline-none focus:ring-red-500 focus:border-red-500': form.errors.employmentDate, 'focus:ring-blumilk-500 focus:border-blumilk-500 sm:text-sm border-gray-300': !form.errors.employmentDate }"
                    />
                    <p
                        v-if="form.errors.employmentDate"
                        class="mt-2 text-sm text-red-600"
                    >
                        {{ form.errors.employmentDate }}
                    </p>
                </div>
            </div>
            <div class="flex justify-end py-3">
                <div class="space-x-3">
                    <InertiaLink
                        href="/users"
                        class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blumilk-500"
                    >
                        Anuluj
                    </InertiaLink>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blumilk-600 hover:bg-blumilk-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blumilk-500"
                    >
                        Zapisz
                    </button>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
import {useForm} from '@inertiajs/inertia-vue3';
import FlatPickr from 'vue-flatpickr-component';

export default {
    employmentDate: 'UserCreate',
    components: {
        FlatPickr,
    },
    props: {
        employmentForms: {
            type: Object,
            default: () => null,
        },
    },
    setup(props) {
        const form = useForm({
            name: null,
            email: null,
            employmentForm: props.employmentForms[0].value,
            employmentDate: new Date(),
        });

        return { form };
    }
};
</script>
