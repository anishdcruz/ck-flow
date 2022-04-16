<template>
	<span :class="`icon icon-${name}`"></span>
</template>
<script type="text/javascript">
	export default {
		name: 'XIcon',
		props: {
			name: {
				type: String,
				required: true
			},
			size: {
				type: [String, Number],
				default: 13
			}
		}
	}
</script>