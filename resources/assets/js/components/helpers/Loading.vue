<template>
	<div :class="['loading', white ? 'loading-white': '']">
    <div class="ball-beat">
      <div></div>
      <div></div>
      <div></div>
    </div>
  </div>
</template>
<script>
  export default {
  	name: 'XLoading',
    props: {
      white: {
        type: Boolean,
        default: false
      }
    }
  }
</script>