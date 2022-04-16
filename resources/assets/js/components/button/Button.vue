<template>
	<button :class="[
			'btn',
      type ? `btn-${type}` : '',
      size ? `btn-${size}` : ''
    ]"
    :disabled="disabled || loading"
    type="button"
		@click="handleClick">
		<span :class="[`icon icon-${icon}`, $slots.default ? 'btn-icon' : '']" v-if="icon && !loading"></span>
		<span class="btn-text" v-if="$slots.default && !loading">
			<slot></slot>
		</span>
		<x-loading white v-if="loading"></x-loading>
	</button>
</template>
<script>
	export default {
		name: 'XButton',
		props: {
			type: {
			  type: String,
			  default: 'default'
			},
			size: String,
			icon: {
				default: null
			},
			disabled: {
			  type: Boolean,
			  default: false
			},
			loading: {
			  type: Boolean,
			  default: false
			}
		},
		methods: {
		  handleClick (evt) {
		    this.$emit('click', evt)
		  }
		}
	}
</script>