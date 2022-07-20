<template>
  <textarea
    ref="textarea"
    v-model="value"
    :rows="rows"
    @input="resizeTextarea"
  />
</template>

<script setup>
import { computed, ref, onMounted } from 'vue'
const props = defineProps({
  modelValue: [String, Number],
  rows: {
    type: Number,
    default: 1,
  },
  resize: {
    type: Boolean,
    default: false,
  },
})
const textarea = ref(null)
const emit = defineEmits(['update:modelValue'])
const value = computed({
  get: () => props.modelValue,
  set: (value) => {
    emit('update:modelValue', value)
  },
})

onMounted(() => {
  textarea.value.style.minHeight = textarea.value.scrollHeight + 'px'
})

function resizeTextarea(){
  if(props.resize){
    textarea.value.style.height = '0'
    textarea.value.style.height = textarea.value.scrollHeight + 'px'
  }
}
</script>