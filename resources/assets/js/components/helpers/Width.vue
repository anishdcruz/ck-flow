<template>
  <div :class="['control', size ? `control-${size}` : '']"
  	@mouseenter="isHovering = true"
  	@mouseleave="isHovering = false">
  	<div class="control-width-label">{{ selected }}%</div>
  	<div :class="[`control-notch notch-${x}`, selected >= x ? 'filled': '']"
  			v-for="x in [25, 33, 50, 66, 75, 100]"
  			@mouseenter.stop="hoveringOver = x"
  			@click="select(x)"></div>
  </div>
</template>
<script>
  export default {
  	name: 'XWidth',
  	model: {
  	  prop: 'value',
  	  event: 'input'
  	},
  	props: {
      size: {
        default: null
      },
  	  value: [String, Number]
  	},
  	data() {
  		return {
  			isHovering: false,
  			hoveringOver: this.value
  		}
  	},
  	computed: {
  		selected() {
  		    if (this.isHovering) {
  		        return this.hoveringOver;
  		    }

  		    return this.value;
  		}
  	},
  	methods: {
  		select(x) {
  			this.$emit('input', x)
  		}
  	}
  }
</script>