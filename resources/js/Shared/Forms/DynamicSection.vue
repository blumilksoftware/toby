<template>
  <div>
    <h3 class="text-lg font-medium leading-6 text-gray-900">
      {{ header }}
    </h3>
    <Draggable
      v-model="items"
      class="pt-4 space-y-4"
      tag="transition-group"
      ghost-class="opacity-50"
      handle=".handle"
      :animation="200"
      :component-data="{tag: 'div', type: 'transition-group'}"
      :item-key="((item) => items.indexOf(item))"
    >
      <template #item="{ element, index }">
        <div class="group flex items-start space-x-3">
          <button
            class="py-4 text-red-500 hover:text-gray-600 opacity-100 group-hover:opacity-100 transition-opacity hover:scale-110 lg:opacity-0 handle"
            type="button"
          >
            <ViewGridIcon class="w-5 h-5 text-gray-500" />
          </button>
          <Disclosure
            v-slot="{ open }"
            as="div"
            class="flex-1 border border-gray-200"
          >
            <div class="flex">
              <DisclosureButton class="transition transition-colors rounded-md group w-full max-w-full overflow-hidden flex items-center justify-between p-4 font-semibold text-gray-500 hover:text-blumilk-500 transition transition-colors rounded-md focus:outline-none">
                <div class="break-all line-clamp-1 text-md">
                  <slot
                    name="itemHeader"
                    :element="element"
                    :index="index"
                  />
                </div>
                <div class="ml-2">
                  <svg
                    :class="[open ? '-rotate-90' : 'rotate-90', 'h-6 w-6 transform transition-transform ease-in-out duration-150']"
                    viewBox="0 0 20 20"
                  >
                    <path
                      d="M6 6L14 10L6 14V6Z"
                      fill="currentColor"
                    />
                  </svg>
                </div>
              </DisclosureButton>
            </div>
            <DisclosurePanel
              as="div"
              class="py-2 px-4 border-t border-gray-200"
            >
              <slot
                name="form"
                :element="element"
                :index="index"
              />
            </DisclosurePanel>
          </Disclosure>
          <button
            class="py-4 text-red-500 hover:text-red-600 opacity-100 group-hover:opacity-100 transition-opacity hover:scale-110 lg:opacity-0"
            type="button"
            @click="removeItem(index)"
          >
            <TrashIcon class="w-5 h-5 text-red-500" />
          </button>
        </div>
      </template>
    </Draggable>
    <div class="px-8">
      <button
        type="button"
        class="p-4 mx-auto mt-4 w-full font-semibold text-center text-blumilk-600 hover:bg-blumilk-25 focus:outline-none transition-colors"
        @click="addItem()"
      >
        {{ addLabel }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { Disclosure, DisclosureButton, DisclosurePanel } from '@headlessui/vue'
import { TrashIcon, ViewGridIcon } from '@heroicons/vue/outline'
import Draggable from 'vuedraggable'
import { computed } from 'vue'

const props = defineProps({
  header: String,
  addLabel: String,
  modelValue: Object,
  itemHeader: [Function, String],
})

const emit = defineEmits(['update:modelValue', 'addItem', 'removeItem'])

const items = computed({
  get: () => props.modelValue,
  set: (value) => {
    emit('update:modelValue', value)
  },
})

function addItem() {
  emit('addItem')
}

function removeItem(index) {
  emit('removeItem', index)
}
</script>