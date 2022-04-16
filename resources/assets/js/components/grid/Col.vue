<template>
  <div :class="[
      `col-${span}`,
      offset ? `col-offset-${offset}` : ''
    ]"
  >
    <slot></slot>
  </div>
</template>
<script>
export default {
  name: 'XCol',
  props: {
    span: {
      type: [Number, String],
      required: true
    },
    offset: {
      type: [Number, String],
      default: null
    }
  }
}
</script>
