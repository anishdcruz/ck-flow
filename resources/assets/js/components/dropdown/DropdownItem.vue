<template>
	<div>
		<div class="dropdown-divide" v-if="divide"></div>
		<div class="dropdown-item">
			<slot></slot>
		</div>
	</div>
</template>
<script>
  export default {
  	name: 'XDropdownItem',
  	props: {
  		divide: {
  			type: Boolean,
  			default: false
  		}
  	}
  }
</script>