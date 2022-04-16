<template>
	<x-modal :footer="false" width="960" @cancel="$emit('cancel')">
		<div slot="title">
			<span>{{$t('available_variables')}}</span>
			<small>({{$t('click_to_copy')}})</small>
		</div>
		<div>
			<div v-for="(vs, key) in base">
				<variable-list :title="$t(key)" :open="false">
					<ul class="variable-list">
						<li class="variable-list-33" v-for="v in vs">
							<span class="icon icon-chevron-right"></span>
							<a @click.stop="copy(`{${v}}`)">
								{<span>{{v}}</span>}
							</a>
						</li>
					</ul>
				</variable-list>
			</div>
			<variable-list :title="$t('header')">
				<ul class="variable-list">
					<li class="variable-list-33" v-for="v in getVars(form.header_content_fields, 'header')">
						<span class="icon icon-chevron-right"></span>
						<a @click.stop="copy(`{${v}}`)">
							{<span>{{v}}</span>}
						</a>
					</li>
				</ul>
			</variable-list>
			<variable-list :title="$t('footer')">
				<ul class="variable-list">
					<li class="variable-list-33" v-for="v in getVars(form.footer_content_fields, 'footer')">
						<span class="icon icon-chevron-right"></span>
						<a @click.stop="copy(`{${v}}`)">
							{<span>{{v}}</span>}
						</a>
					</li>
				</ul>
			</variable-list>
			<variable-list v-for="(page, pI) in form.pages"
				:key="page.name"
				:title="page.title"
				 :open="pI === index">
				<br><small>{{$t('content_fields')}}</small><br>
				<ul class="variable-list">
					<li class="variable-list-33" v-for="v in getVars(page.content_fields, `${page.name}.cf`)">
						<span class="icon icon-chevron-right"></span>
						<a @click.stop="copy(`{${v}}`)">
							{<span>{{v}}</span>}
						</a>
					</li>
				</ul>
				<small>{{$t('user_fields')}}</small><br>
				<ul class="variable-list">
					<li class="variable-list-33" v-for="v in getVars(page.user_fields, `${page.name}.uf`)">
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
			type: Number,
			form: Object,
			index: {
				type: Number,
				default: null
			}
		},
		computed: {
			base() {
				return this.shared.template_variables[this.type]
			},
			getVars() {
				return (collection, page) => {
					const f = []
					collection.forEach((section) => {
						return section.fields.forEach((field) => {
							f.push(`${page}.${section.name}.${field.name}`)
						})
					})
					return f
				}
			}
		},
		data() {
			return {
				shared: shared,
			}
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