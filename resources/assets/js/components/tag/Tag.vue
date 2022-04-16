<template>
	<transition name="fade">
	  <div :class="[
				'tag',
	      type ? `tag-${type}` : ''
	    ]">
	  	<span class="tag-text">
	  		<slot></slot>
	  	</span>
	  	<span class="icon icon-x tag-close" v-if="closable"
	  		@click="handleClose"
	  		@keydown.enter="handleClose"
	  		@keydown.space="handleClose" tabindex="0"></span>
	  </div>
	</transition>
</template>
<script>
  export default {
  	name: 'XTag',
  	props: {
  		type: {
			  type: String,
			  default: 'default'
			},
			closable: {
			  type: Boolean,
			  default: false
			}
  	},
  	methods: {
  		handleClose(e) {
  			this.$emit('close', e)
  		}
  	}
  }
</script>