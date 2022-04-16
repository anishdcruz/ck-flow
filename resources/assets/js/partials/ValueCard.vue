<template>
	<div class="metric-card">
		<div class="metric-card-inner">
			<div class="metric-card-title">
				<span>{{label}}</span>
				<i class="metric-card-trash icon icon-trash-b"
					v-if="edit"
					@click="$emit('remove')"></i>
			</div>
			<p class="metric-card-value">{{value}}</p>
		</div>
	</div>
</template>
<script>
	export default {
		props: {
			edit: {
				type: Boolean,
				default: false
			},
			label: String,
			value: [Number, String]
		}
	}
</script>