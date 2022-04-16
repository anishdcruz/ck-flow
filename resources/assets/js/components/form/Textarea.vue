<template>
<textarea class="form-input" :value="value"
    @input="handleInput" :placeholder="placeholder"
    :disabled="disabled"></textarea>
</template>
<script>
  export default {
    name: 'XTextarea',
    model: {
      prop: 'value',
      event: 'input'
    },
    props: {
      value: [String, Number],
      placeholder: String,
      disabled: {
        type: Boolean,
        default: false
      }
    },
    methods: {
      handleInput(e) {
        this.$emit('input', e.target.value)
      }
    }
  }
</script>