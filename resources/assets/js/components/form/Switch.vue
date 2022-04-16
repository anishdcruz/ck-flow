<template>
	<transition name="fade">
  	<div class="switch" :tabindex="disabled ? -1 : tabindex" :class="[{'switch-checked': value, 'switch-disabled': disabled}]"
      @click="toggleSwitch" @keydown.space.prevent="toggleSwitch">
    </div>
  </transition>
</template>
<script>
  export default {
  	name: 'XSwitch',
  	model: {
  	  prop: 'value',
  	  event: 'input'
  	},
  	props: {
  	  value: [Boolean, Number],
  	  disabled: {
  	    type: Boolean,
  	    default: false
  	  },
      tabindex: {
        default: 0
      }
  	},
  	methods: {
  	  toggleSwitch(e) {
  	  	if(this.disabled) return
  	    this.$emit('input', !this.value)
  	  }
  	}
  }
</script>