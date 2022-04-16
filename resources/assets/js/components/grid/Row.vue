<template>
	<div :class="[
		'row',
		gutter ? 'row-no-gutter' : '',
		line ? 'row-line' : ''
	]">
		<slot></slot>
	</div>
</template>

<script>
	export default {
		name: 'XRow',
		props: {
			gutter: {
				type: [Boolean, String],
				default: false
			},
			line: {
				type: Boolean,
				default: false
			}
		}
	}
</script>