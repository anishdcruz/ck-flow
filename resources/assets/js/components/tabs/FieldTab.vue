<template>
  <div>
  	<div :class="['field-tabs', `field-tabs-${align}`]">
  		<div :class="['field-tabs-item', tab && tab.name === activeTab ? 'field-tabs-active' : '']"
  			v-for="tab in tabs">
  			<span class="field-tabs-btn" @click="setActive(tab)">{{tab.title}}</span>
  		</div>
  	</div>
    <slot :name="tab.name" v-for="tab in tabs" v-if="tab && tab.name === activeTab" />
  </div>
</template>
<script>
  export default {
    name: 'XFieldTab',
  	props: {
  		tabs: Array,
      align: {
        default: 'center'
      }
  	},
  	data() {
  	  return {
  	    active: null
  	  }
  	},
  	computed: {
  		activeTab() {
  			if(this.active) {
  				return this.active
  			} else {
  				return this.tabs[0].name
  			}
  		}
  	},
  	methods: {
  		setActive(tab) {
  			this.active = tab.name
  		}
  	}
  }
</script>