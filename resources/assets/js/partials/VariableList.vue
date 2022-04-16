<template>
	<div class="variable">
		<strong class="variable-title" @click="show = !show">
			<div>{{title}}</div>
			<div>
				<span
					:class="[`variable-extra icon icon-chevron-${showList ? 'up' : 'down'}`]"></span>
			</div>
		</strong>
		<slot v-if="showList"></slot>
	</div>
</template>
<script>
	export default {
		props: {
			title: String,
			open: {
				default: false
			}
		},
		computed: {
			showList() {
				return this.show
			}
		},
		data() {
			return {
				show: this.open
			}
		}
	}
</script>