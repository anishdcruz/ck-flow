<template>
	<transition name="fade">
	  <div :class="[
				'status',
	      id ? `status-${colorClass}` : ''
	    ]">
	  	<span class="status-text">
	  		{{status}}
	  	</span>
	  </div>
	</transition>
</template>
<script>
  export default {
  	name: 'XStatus',
  	props: {
  		id: {
			  type: String,
			  default: 'default'
			},
			color: {
				type: String,
				default: null
			}
		},
		computed: {
			status() {
				return this.$t(this.id)
			},
			colorClass() {
				return this.color || this.id
			}
		}
  }
</script>