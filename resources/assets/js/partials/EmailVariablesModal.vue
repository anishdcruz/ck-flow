<template>
	<x-modal :footer="false" width="960" @cancel="$emit('cancel')">
		<div slot="title">
			<span>{{$t('available_variables')}}</span>
			<small>({{$t('click_to_copy')}})</small>
		</div>
		<div>
			<variable-list :title="$t('default')" :open="true">
				<ul class="variable-list">
					<li class="variable-list-33" v-for="v in items">
						<span class="icon icon-chevron-right"></span>
						<a @click.stop="copy(`{${v}}`)">
							{<span>{{v}}</span>}
						</a>
					</li>
				</ul>
			</variable-list>
		</div>
	</x-modal>
</template>
<script>
	import shared from '@js/shared'
	import VariableList from '@js/partials/VariableList.vue'
	import clipboard from 'clipboard-polyfill'
	export default {
		components: { VariableList },
		props: {
			items: Array
		},
		methods: {
			copy(v) {
				clipboard.writeText(v)
					.then(() => {
						this.$message.success(`${v} ${this.$t('copied')}`)
						this.$emit('cancel')
					})
			}
		}
	}
</script>