<template>
	<div :class="[
		'panel',
		margin ? 'panel-margin' : ''
	]">
		<div class="progress-line" v-if="loading"></div>
		<div class="panel-heading" v-if="$slots.title || $slots.extra">
			<div class="panel-title" v-if="$slots.title">
				<slot name="title"></slot>
			</div>
			<div class="panel-extra" v-if="$slots.extra">
				<slot name="extra"></slot>
			</div>
		</div>
		<div :class="[
			!padding ? 'panel-body' : 'panel-form'
		]" v-if="$slots.default">
			<slot></slot>
		</div>
		<div :class="[
			'panel-footer',
			alt ? 'panel-alt' : ''
		]" v-if="$slots.footer">
			<slot name="footer"></slot>
		</div>
	</div>
</template>

<script type="text/javascript">
	export default {
		name: 'XPanel',
		props: {
			margin: {
				type: [Boolean, String],
				default: false
			},
			alt: {
				type: [Boolean, String],
				default: false
			},
			loading: {
			  type: Boolean,
			  default: false
			},
			padding: {
				type: Boolean,
				default: false
			}
		}
	}
</script>