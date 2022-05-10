<template>
  <Combobox
    as="div"
    nullable
    multiple
  >
    <ComboboxLabel class="block text-sm font-medium text-gray-700">
      {{ label }}
    </ComboboxLabel>
    <div class="relative mt-2">
      <ComboboxInput
        as="template"
        class="w-full h-12 rounded-md border border-gray-300 bg-white py-2 pl-3 pr-10 shadow-sm focus:border-blumilk-500 focus:outline-none focus:ring-1 focus:ring-blumilk-500 sm:text-sm"
        :display-value="(item) => item"
        @change="query = $event.target.value"
      >
        <span>aee</span>
      </ComboboxInput>
      <ComboboxButton class="absolute inset-y-0 right-0 flex items-center rounded-r-md px-2 focus:outline-none">
        <SelectorIcon class="h-5 w-5 text-gray-400" />
      </ComboboxButton>

      <ComboboxOptions
        v-if="filteredItems.length"
        class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm"
      >
        <ComboboxOption
          v-for="item in filteredItems"
          :key="item.id"
          v-slot="{ active, selected }"
          :value="item"
          as="template"
        >
          <li :class="['relative cursor-default select-none py-2 pl-3 pr-9', active ? 'bg-blumilk-600 text-white' : 'text-gray-900']">
            <span :class="['block truncate', selected && 'font-semibold']">
              {{ item.name }}
            </span>

            <span
              v-if="selected"
              :class="['absolute inset-y-0 right-0 flex items-center pr-4', active ? 'text-white' : 'text-blumilk-600']"
            >
              <CheckIcon class="h-5 w-5" />
            </span>
          </li>
        </ComboboxOption>
      </ComboboxOptions>
    </div>
  </Combobox>
</template>

<script setup>
import { computed, ref } from 'vue'
import { CheckIcon, SelectorIcon } from '@heroicons/vue/solid'
import {
  Combobox,
  ComboboxButton,
  ComboboxInput,
  ComboboxLabel,
  ComboboxOption,
  ComboboxOptions,
} from '@headlessui/vue'

const props = defineProps({
  label: null,
  items: Array,
})

const query = ref('')

const filteredItems = computed(() =>
  query.value === ''
    ? props.items
    : props.items.filter((item) => item.name.toLowerCase().includes(query.value.toLowerCase())),
)
</script>