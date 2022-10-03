<script setup>
import FlatPickr from 'vue-flatpickr-component'
import monthSelectPlugin from 'flatpickr/dist/plugins/monthSelect'

const config = {
  plugins: [
    new monthSelectPlugin({
      shorthand: true,
      dateFormat: 'm/Y',
    }),
  ],
}
</script>

<template>
  <FlatPickr :config="config" />
</template>

