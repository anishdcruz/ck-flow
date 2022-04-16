<template>
  <transition name="fade">
  	  <div  :class="[
  				'alert',
  	      type ? `alert-${type}` : ''
  	    ]">
  	  	<div class="alert-inner">
  	  		<div class="alert-icon">
  	  			<span class="icon icon-alert-circle"></span>
  	  		</div>
  	  		<div class="alert-text">{{message}}</div>
  	  		<div class="alert-close" v-if="closable" @click="handleClose">
  	  			<span class="icon icon-x"></span>
  	  		</div>
  	  	</div>
  	  </div>
  </transition>
</template>
<script>
  export default {
  	name: 'XAlert',
  	props: {
  		message: String,
  		type: {
  			default: 'success'
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