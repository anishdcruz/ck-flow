<template>
	<div class="content-inner" v-if="show">
		<x-panel padding margin>
			<div slot="title">
				<router-link to="/expenses">{{$t('expenses')}}</router-link> / {{model.number}}
			</div>
			<div slot="extra">
				<router-link :to="`/expenses`" class="btn btn-default btn-sm">
					<small class="icon icon-arrow-left-c"></small>
				</router-link>

				<x-button size="sm" type="error" icon="trash-b" @click="removeDB('expenses', model.id)"></x-button>
			</div>
			<x-row line>
				<x-group col="8" label="number">
					<p>{{model.number}}</p>
				</x-group>
				<x-group col="8" label="vendor">
					<p v-if="model.vendor">
						<router-link :to="`/vendors/${model.vendor_id}`">
							{{model.vendor.name}}
						</router-link>
					</p>
				</x-group>
				<x-group col="8" label="date">
					<p>{{model.date | toDate}}</p>
				</x-group>
				<x-group col="8" label="category">
					<p>{{model.category.name}}</p>
				</x-group>
				<x-group col="8" label="description">
					<pre>{{model.description}}</pre>
				</x-group>
			</x-row>
			<x-row line>
				<x-group col="8" label="amount">
					<p>{{model.amount | formatMoney}}</p>
				</x-group>
				<x-group col="8" label="project">
					<p v-if="model.project">
						<router-link :to="`/projects/${model.project_id}`">
							{{model.project.number}}
						</router-link>
					</p>
					<p v-else>-</p>
				</x-group>
				<x-group col="8" label="opportunity">
					<p v-if="model.opportunity">
						<router-link :to="`/opportunities/${model.opportunity_id}`">
							{{model.opportunity.number}}
						</router-link>
					</p>
					<p v-else>-</p>
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
				model: {
					notes: [],
					contact: {},
					proposal: {},
					catgory: {},
					status: {},
					lines: []
				}
			}
		}
	}
</script>