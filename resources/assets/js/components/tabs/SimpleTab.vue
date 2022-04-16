<template>
  <div>
    <slot :name="tab" v-for="tab in tabs" v-if="tab === activeTab" />
  </div>
</template>
<script>
  export default {
  	name: 'XSimpleTab',
  	props: {
  		active: String,
  		tabs: Array
  	},
  	computed: {
  		activeTab() {
  			if(this.active) {
  				return this.active
  			} else {
  				return this.tabs[0]
  			}
  		}
  	}
  }
</script>