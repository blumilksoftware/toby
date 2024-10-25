<script setup>
import { useForm } from '@inertiajs/vue3'
import FlatPickr from 'vue-flatpickr-component'
import { Listbox, ListboxButton, ListboxLabel, ListboxOption, ListboxOptions, Switch } from '@headlessui/vue'
import { CheckIcon, ChevronUpDownIcon } from '@heroicons/vue/24/solid'
import MultipleCombobox from '@/Shared/Forms/MultipleCombobox.vue'
import InertiaLink from '@/Shared/InertiaLink.vue'
import AppLayout from '@/Shared/Layout/AppLayout.vue'

const props = defineProps({
  users: Object,
  labels: Array,
})

const form = useForm({
  idNumber: null,
  name: null,
  labels: [],
  isMobile: false,
  assignee: null,
  assignedAt: null,
})

function createEquipmentItem() {
  form
    .transform(data => ({
      ...data,
      assignee: data.assignee?.id,
    }))
    .post('/equipment-items')
}
</script>

<template>
  <AppLayout>
    <template #title>Dodawanie sprzętu</template>
    <div class="mx-auto w-full max-w-7xl bg-white shadow-md">
      <div class="p-4 sm:px-6">
        <h2 class="text-lg font-medium leading-6 text-gray-900">
          Dodaj sprzęt
        </h2>
      </div>
      <form
        class="px-6 border-t border-gray-200"
        @submit.prevent="createEquipmentItem"
      >
        <div class="items-center py-4 sm:grid sm:grid-cols-3">
          <label
            for="idNumber"
            class="block text-sm font-medium text-gray-700 sm:mt-px"
          >
            ID
          </label>
          <div class="mt-1 sm:col-span-2 sm:mt-0">
            <input
              id="idNumber"
              v-model="form.idNumber"
              type="text"
              class="block w-full max-w-lg rounded-md shadow-sm sm:text-sm"
              :class="{ 'border-red-300 text-red-900 focus:outline-none focus:ring-red-500 focus:border-red-500': form.errors.idNumber, 'focus:ring-blumilk-500 focus:border-blumilk-500 sm:text-sm border-gray-300': !form.errors.idNumber }"
            >
            <p
              v-if="form.errors.idNumber"
              class="mt-2 text-sm text-red-600"
            >
              {{ form.errors.idNumber }}
            </p>
          </div>
        </div>
        <div class="items-center py-4 sm:grid sm:grid-cols-3">
          <label
            for="name"
            class="block text-sm font-medium text-gray-700 sm:mt-px"
          >
            Nazwa
          </label>
          <div class="mt-1 sm:col-span-2 sm:mt-0">
            <input
              id="name"
              v-model="form.name"
              type="text"
              class="block w-full max-w-lg rounded-md shadow-sm sm:text-sm"
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
        <div
          class="items-center py-4 sm:grid sm:grid-cols-3"
        >
          <label
            for="isMobile"
            class="block text-sm font-medium text-gray-700"
          >
            Mobilny
          </label>
          <div class="mt-2 sm:col-span-2 sm:mt-0">
            <Switch
              id="isMobile"
              v-model="form.isMobile"
              :class="[form.isMobile ? 'bg-blumilk-500' : 'bg-gray-200', 'relative inline-flex shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blumilk-500']"
            >
              <span
                :class="[form.isMobile ? 'translate-x-5' : 'translate-x-0', 'pointer-events-none inline-block size-5 rounded-full bg-white shadow ring-0 transition ease-in-out duration-200']"
              />
            </Switch>
          </div>
        </div>
        <Listbox
          v-model="form.assignee"
          as="div"
          class="items-center py-4 sm:grid sm:grid-cols-3"
        >
          <ListboxLabel class="block text-sm font-medium text-gray-700">
            Przydzielona osoba
          </ListboxLabel>
          <div class="relative mt-1 sm:col-span-2 sm:mt-0">
            <ListboxButton
              class="relative py-2 pr-10 pl-3 w-full max-w-lg text-left bg-white rounded-md border focus:outline-none focus:ring-1 shadow-sm cursor-default sm:text-sm"
              :class="{ 'border-red-300 text-red-900 focus:ring-red-500 focus:border-red-500': form.errors.assignee, 'focus:ring-blumilk-500 focus:border-blumilk-500 sm:text-sm border-gray-300': !form.errors.assignee }"
            >
              <span
                v-if="form.assignee"
                class="flex items-center"
              >
                <img
                  :src="form.assignee.avatar"
                  class="shrink-0 size-6 rounded-full"
                >
                <span class="block ml-3 truncate">{{ form.assignee.name }}</span>
              </span>
              <span
                v-else
                class="block truncate"
              >
                Wybierz osobę
              </span>
              <span class="flex absolute inset-y-0 right-0 items-center pr-2 pointer-events-none">
                <ChevronUpDownIcon class="size-5 text-gray-400" />
              </span>
            </ListboxButton>

            <transition
              leave-active-class="transition ease-in duration-100"
              leave-from-class="opacity-100"
              leave-to-class="opacity-0"
            >
              <ListboxOptions
                class="overflow-auto absolute z-10 py-1 mt-1 w-full max-w-lg max-h-60 text-base bg-white rounded-md focus:outline-none ring-1 ring-black/5 shadow-lg sm:text-sm"
              >
                <ListboxOption
                  v-for="user in props.users.data"
                  :key="user.id"
                  v-slot="{ active, selected }"
                  as="template"
                  :value="user"
                >
                  <li :class="[active ? 'bg-gray-100' : 'text-gray-900', 'cursor-default select-none relative py-2 pl-3 pr-9']">
                    <div class="flex items-center">
                      <img
                        :src="user.avatar"
                        alt=""
                        class="shrink-0 size-6 rounded-full"
                      >
                      <span :class="[selected ? 'font-semibold' : 'font-normal', 'ml-3 block truncate']">
                        {{ user.name }}
                      </span>
                    </div>

                    <span
                      v-if="selected"
                      :class="['text-blumilk-600 absolute inset-y-0 right-0 flex items-center pr-4']"
                    >
                      <CheckIcon class="size-5" />
                    </span>
                  </li>
                </ListboxOption>
                <ListboxOption
                  as="template"
                  :value="null"
                >
                  <li class="text-gray-900 cursor-default select-none relative py-2 pl-3 pr-9 hover:bg-gray-100 ">
                    <span class="font-normal ml-9 block truncate">
                      Brak
                    </span>
                  </li>
                </ListboxOption>
              </ListboxOptions>
            </transition>
            <p
              v-if="form.errors.assignee"
              class="mt-2 text-sm text-red-600"
            >
              {{ form.errors.assignee }}
            </p>
          </div>
        </Listbox>
        <div class="items-center py-4 sm:grid sm:grid-cols-3">
          <label
            for="date_from"
            class="block text-sm font-medium text-gray-700 sm:mt-px"
          >
            Data przydzielenia
            <span class="text-xs text-gray-500">
              (opcjonalna)
            </span>
          </label>
          <div class="mt-1 sm:col-span-2 sm:mt-0">
            <FlatPickr
              id="date_from"
              v-model="form.assignedAt"
              class="block w-full max-w-lg rounded-md shadow-sm sm:text-sm"
              :class="{ 'border-red-300 text-red-900 focus:outline-none focus:ring-red-500 focus:border-red-500': form.errors.assignedAt, 'focus:ring-blumilk-500 focus:border-blumilk-500 sm:text-sm border-gray-300': !form.errors.assignedAt }"
            />
            <p
              v-if="form.errors.assignedAt"
              class="mt-2 text-sm text-red-600"
            >
              {{ form.errors.assignedAt }}
            </p>
          </div>
        </div>
        <div class="items-center py-4 sm:grid sm:grid-cols-3">
          <label
            for="date_from"
            class="block text-sm font-medium text-gray-700 sm:mt-px"
          >
            Etykiety
            <span class="text-xs text-gray-500">
              (opcjonalne)
            </span>
          </label>
          <div class="mt-1 sm:col-span-2 sm:mt-0 w-full max-w-lg">
            <MultipleCombobox
              v-model="form.labels"
              :show-chips="true"
              :items="labels"
            />
            <p
              v-if="form.errors.labels"
              class="mt-2 text-sm text-red-600"
            >
              {{ form.errors.labels }}
            </p>
          </div>
        </div>
        <div class="flex justify-end py-3">
          <div class="space-x-3">
            <InertiaLink
              href="/equipment-items"
              class="py-2 px-4 text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blumilk-500 focus:ring-offset-2 shadow-sm"
            >
              Anuluj
            </InertiaLink>
            <button
              type="submit"
              class="inline-flex justify-center py-2 px-4 text-sm font-medium text-white bg-blumilk-600 rounded-md border border-transparent focus:outline-none focus:ring-2 focus:ring-blumilk-500 focus:ring-offset-2 shadow-sm"
              :class="[form.processing || !form.isDirty ? 'disabled:opacity-60' : 'hover:bg-blumilk-700']"
              :disabled="form.processing || !form.isDirty"
            >
              Dodaj
            </button>
          </div>
        </div>
      </form>
    </div>
  </AppLayout>
</template>
