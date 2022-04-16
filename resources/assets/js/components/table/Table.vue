<template>
  <table>
  	<slot></slot>
  	<tfoot v-if="$slots.tfoot">
  		<slot name="tfoot"></slot>
  	</tfoot>
  </table>
</template>
<script>
  export default {
  	name: 'XTable'
  }
</script>