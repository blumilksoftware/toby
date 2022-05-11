<template>
  <RadioGroup v-model="selectedValue">
    <div
      :class="`relative overflow-hidden flex h-12 rounded-l-md rounded-r-md space-x-px ${selectedValue.activeColor} transition-colors duration-200 easy-in-out`"
    >
      <RadioGroupOption
        v-for="(level, index) in levels"
        :key="index"
        as="template"
        :value="level"
      >
        <div
          :class="`${selectedValue.backgroundColor} hover:opacity-80 cursor-pointer transition-colors duration-200 easy-in-out focus:outline-none flex-1`"
        />
      </RadioGroupOption>
      <div
        :class="`absolute transform transition-transform  duration-200 easy-in-out`"
        :style="`width: ${100/levels.length}%; transform: translateX(calc(${100 * currentIndex}% - 1px))`"
      >
        <div :class="`h-12 ${selectedValue.activeColor} transition-colors duration-300 easy-in-out`" />
      </div>
    </div>
  </RadioGroup>
</template>

<script setup>
import { RadioGroup, RadioGroupOption } from '@headlessui/vue'
import { computed } from 'vue'

const emit = defineEmits(['update:modelValue'])

const props = defineProps({
  levels: Array,
  modelValue: Object,
})

const selectedValue = computed({
  get: () => props.modelValue,
  set: (value) => {
    emit('update:modelValue', value)
  },
})

const currentIndex = computed(() => props.levels.findIndex((level) => level.level === selectedValue.value.level))

</script>