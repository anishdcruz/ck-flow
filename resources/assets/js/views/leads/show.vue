<template>
	<div class="content-inner" v-if="show">
		<x-panel padding>
			<div slot="title">
				<router-link to="/leads">{{$t('lead')}}</router-link> / {{model.person}}
				<small>({{model.number}})</small>
			</div>
			<div slot="extra">
				<router-link :to="`/leads`" class="btn btn-default btn-sm">
					<small class="icon icon-arrow-left-c"></small>
				</router-link>
				<x-dropdown size="sm" dir="right" icon="more">
					<x-dropdown-menu slot="menu">
						<x-dropdown-item v-for="(status, i) in model.all_status"
							:key="status.id"
							@click.native="markAs(status.id)">
							{{$t('mark_as')}} {{status.name}}
						</x-dropdown-item>

						<x-dropdown-link divide :to="`/leads/${model.id}/edit`">
							{{$t('edit')}}
						</x-dropdown-link>
						<x-dropdown-item>
							<a @click.stop="removeDB('leads', model.id)">{{$t('delete')}}</a>
						</x-dropdown-item>
					</x-dropdown-menu>
				</x-dropdown>
			</div>
			<x-row line>
				<x-group col="8" label="number">
					<p>{{model.number}}</p>
				</x-group>
				<x-group col="8" label="person">
					<p>{{model.person || '-'}}</p>
				</x-group>
				<x-group col="8" label="email">
					<p>{{model.email || '-'}}</p>
				</x-group>
				<x-group col="8" label="organization">
					<p v-if="model.organization">
						{{model.organization}}
					</p>
					<p v-else>-</p>
				</x-group>
				<x-group col="8" label="title">
					<p>{{model.title || '-'}}</p>
				</x-group>
				<x-group col="8" label="department">
					<p>{{model.department || '-'}}</p>
				</x-group>
			</x-row>
			<x-row line>
				<x-group col="8" label="phone">
					<p>{{model.phone || '-'}}</p>
				</x-group>
				<x-group col="8" label="mobile">
					<p>{{model.mobile || '-'}}</p>
				</x-group>
				<x-group col="8" label="fax">
					<p>{{model.fax || '-'}}</p>
				</x-group>
				<x-group col="8" label="website">
					<p>{{model.website || '-'}}</p>
				</x-group>
			</x-row>
			<x-row line>
				<x-group col="8" label="primary_address">
					<pre>{{model.primary_address || '-'}}</pre>
				</x-group>
				<x-group col="8" label="other_address">
					<pre>{{model.other_address || '-'}}</pre>
				</x-group>
			</x-row>
			<x-row v-if="model.custom_fields && model.custom_fields.length > 0" line>
				<custom-field-preview :field="field" :key="field.name"
					v-for="field in model.custom_fields" />
			</x-row>
			<x-row line>
				<x-group col="8" label="created_at">
					<pre>{{model.created_at | toDate}}</pre>
				</x-group>
				<x-group col="8" label="status">
					<span :class="`status status-${model.status.color}`">
						<span class="status-text">{{model.status.name}}</span>
					</span>
				</x-group>
			</x-row>
		</x-panel>
	</div>
</template>
<script>
	import { showable } from '@js/lib/mixins'
	import CustomFieldPreview from '@js/partials/CustomFieldPreview.vue'
	export default {
		mixins: [ showable ],
		components: { CustomFieldPreview },
		data() {
			return {
				show: false,
				activeType: 'text',
				model: {
					status: {},
					all_status: {},
					custom_fields: []
				}
			}
		},
		methods: {
			markAs(type) {
				this.$bar.start()
				this.$http.post(`/api/mark-as/leads/${this.model.id}`, {type: type})
					.then((res) => {
						if(res.data.saved) {
							this.model.status = res.data.status
						}
					})
					.finally((res) => {
						this.$bar.finish()
					})
			}
		}
	}
</script>