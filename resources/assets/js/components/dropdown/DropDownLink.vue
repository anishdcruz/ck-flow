<template>
	<div>
		<div class="dropdown-divide" v-if="divide"></div>
		<router-link class="dropdown-link" :to="to">
			<slot></slot>
		</router-link>
	</div>
</template>
<script>
  export default {
  	name: 'XDropdownLink',
  	props: {
      to: String,
  		divide: {
  			type: Boolean,
  			default: false
  		}
  	}
  }
</script>