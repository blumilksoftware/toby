<script setup>
import { computed } from 'vue'
import { Listbox, ListboxButton, ListboxLabel, ListboxOption, ListboxOptions } from '@headlessui/vue'
import { range } from 'lodash'
import { ChevronUpDownIcon, CheckIcon } from '@heroicons/vue/24/solid'
import { DateTime } from 'luxon'

const props = defineProps({
  modelValue: [String, Number],
  from: Number,
  to: Number,
  nullable: Boolean,
  label: String,
  resetButton: Boolean,
})

const currentDate = DateTime.now()

const emit = defineEmits(['update:modelValue'])
const value = computed({
  get: () => props.modelValue,
  set: (value) => {
    emit('update:modelValue', value)
  },
})
</script>

<template>
  <div>
    <Listbox
      v-model="value"
      as="div"
    >
      <ListboxLabel
        v-if="label"
        class="block mb-2 text-sm font-medium text-gray-700"
      >
        {{ label }}
      </ListboxLabel>
      <div class="relative mt-1 sm:mt-0">
        <ListboxButton
          class="relative py-2 pr-10 pl-3 w-full text-left bg-white rounded-md border border-gray-300 focus:border-blumilk-500 focus:outline-none focus:ring-1 focus:ring-blumilk-500 shadow-sm cursor-default sm:text-sm"
        >
          <span class="flex items-center">
            {{ value !== null || !nullable ? value : 'Wszystkie' }}
          </span>
          <span class="flex absolute inset-y-0 right-0 items-center pr-2 pointer-events-none">
            <ChevronUpDownIcon class="w-5 h-5 text-gray-400" />
          </span>
        </ListboxButton>

        <transition
          leave-active-class="transition ease-in duration-100"
          leave-from-class="opacity-100"
          leave-to-class="opacity-0"
        >
          <ListboxOptions
            class="overflow-auto absolute z-10 py-1 mt-1 max-h-60 text-base bg-white rounded-md focus:outline-none ring-1 ring-black ring-opacity-5 shadow-lg sm:text-sm"
          >
            <ListboxOption
              v-if="nullable"
              v-slot="{ active, selected }"
              :value="null"
              as="template"
            >
              <li
                :class="[active ? 'bg-gray-100' : 'text-gray-900', 'cursor-default truncate select-none relative py-2 pl-3 pr-9']"
              >
                Wszystkie

                <span
                  v-if="selected"
                  :class="['text-blumilk-600 absolute inset-y-0 right-0 flex items-center pr-4']"
                >
                  <CheckIcon class="w-5 h-5" />
                </span>
              </li>
            </ListboxOption>
            <ListboxOption
              v-for="item in range(from, to + 1)"
              :key="item"
              v-slot="{ active, selected }"
              :value="item"
              as="template"
            >
              <li
                :class="[active ? 'bg-gray-100' : 'text-gray-900', 'cursor-default truncate select-none relative py-2 pl-3 pr-9']"
              >
                {{ item }}

                <span
                  v-if="selected"
                  :class="['text-blumilk-600 absolute inset-y-0 right-0 flex items-center pr-4']"
                >
                  <CheckIcon class="w-5 h-5" />
                </span>
              </li>
            </ListboxOption>
          </ListboxOptions>
        </transition>
      </div>
    </Listbox>
    <button
      v-if="resetButton"
      type="button"
      class="font-semibold text-blumilk-600 hover:text-blumilk-500"
      @click="value = currentDate.year"
    >
      Wróć do obecnego roku
    </button>
  </div>
</template>

