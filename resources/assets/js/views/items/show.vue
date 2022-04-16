<template>
	<div class="content-inner" v-if="show">
		<x-panel padding>
			<div slot="title">
				<router-link to="/items">{{$t('item')}}</router-link> / {{model.code}}
			</div>
			<div slot="extra">
				<router-link :to="`/items`" class="btn btn-default btn-sm">
					<small class="icon icon-arrow-left-c"></small>
				</router-link>
				<router-link :to="`/items/${model.id}/edit`" class="btn btn-default btn-sm">
					<small class="icon icon-edit"></small>
				</router-link>
				<x-button size="sm" type="error" icon="trash-b" @click="removeDB('items', model.id)"></x-button>
			</div>
				<x-row line>
					<x-group col="8" label="code">
						<p>{{model.code}}</p>
					</x-group>
					<x-group col="8" label="category">
						<p v-if="model.category">
							{{model.category.name}}
						</p>
					</x-group>
					<x-group col="8" label="uom">
						<p v-if="model.uom">
							{{model.uom.name}}
						</p>
					</x-group>
			</x-row>
			<x-row line>
				<x-group col="8" label="description">
					<pre>{{model.description}}</pre>
				</x-group>
				<x-group col="8" label="unit_price">
					<p>{{model.unit_price | formatMoney}}</p>
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
					custom_fields: [],
					category: {}
				}
			}
		}
	}
</script>