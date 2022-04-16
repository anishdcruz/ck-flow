<template>
	<x-modal width="950" @cancel="$emit('cancel')">

		<div slot="title">
			<div v-if="field">
				<span>{{$t('edit')}} {{activeType && activeType.title}}</span>
			</div>
			<div class="flex flex-center" v-else-if="activeTab === 'field'">
				<x-button size="sm" @click="activeTab ='menu'">
					{{$t('back')}}
				</x-button>
				<span class="field-modal-title">{{activeType.title}}</span>
			</div>
			<div v-else>{{$t('select_field_type')}}</div>
		</div>
		<div>
			<x-simple-tab :active="activeTab" :tabs="availableTabs">
					<div class="field-menu" slot="menu">
						<div class="field-menu-item" v-for="type in types"
							 @click="selectType(type)">
							<div class="field-menu-title">{{type.title}}</div>
						</div>
					</div>
				<div slot="field">
					<x-simple-tab :active="activeType && activeType.name"
						:tabs="availableTypes">
						<text-type :form="form" :tabs="activeType && activeType.tabs" slot="text"></text-type>
						<date-type :form="form" :tabs="activeType && activeType.tabs" slot="date"></date-type>
						<text-type type="number" :form="form" :tabs="activeType && activeType.tabs" slot="number"></text-type>
						<textarea-type :form="form" :tabs="activeType && activeType.tabs" slot="textarea"></textarea-type>
						<select-type :form="form" :tabs="activeType && activeType.tabs" slot="select"></select-type>
						<list-type :form="form" :tabs="activeType && activeType.tabs" slot="list"></list-type>
						<currency-type :form="form" :tabs="activeType && activeType.tabs" slot="currency"></currency-type>
						<image-type :form="form" :tabs="activeType && activeType.tabs" slot="image"></image-type>
						<table-type :form="form" :tabs="activeType && activeType.tabs" slot="table"></table-type>
					</x-simple-tab>
				</div>
			</x-simple-tab>
		</div>
		<template slot="footer">
			<div></div>
			<div>
				<x-button @click="$emit('cancel')">{{$t('cancel')}}</x-button>
				<x-button type="primary" @click="addField"
					v-if="activeTab !=='menu'">
					{{field ? this.$t('ok') : this.$t('add')}}
				</x-button>
			</div>
		</template>
	</x-modal>
</template>
<script>
	import { snakeCase } from 'lodash'
	import TextType from '@js/partials/FieldTypes/TextType.vue'
	import DateType from '@js/partials/FieldTypes/DateType.vue'
	import ImageType from '@js/partials/FieldTypes/ImageType.vue'
	import TextareaType from '@js/partials/FieldTypes/TextareaType.vue'
	import SelectType from '@js/partials/FieldTypes/SelectType.vue'
	import ListType from '@js/partials/FieldTypes/ListType.vue'
	import CurrencyType from '@js/partials/FieldTypes/CurrencyType.vue'
	import TableType from '@js/partials/FieldTypes/TableType.vue'
	export default {
		components: {
			TextType, DateType,
			ImageType,
			TextareaType, SelectType, ListType,
			CurrencyType,
			TableType
		},
		props: {
			field: {
				type: Object,
				default: null
			}
		},
		data() {
			return {
				edit: false,
				activeType: null,
				activeTab: 'menu',
				availableTabs: ['menu', 'field'],
				// todo: need translation
				types: [
					{title: 'Text', name: 'text', tabs: [
						{title: 'Basic', name: 'basic'},
						{title: 'Extra', name: 'extra'}
					]},
					{title: 'Number', name: 'number', tabs: [
						{title: 'Basic', name: 'basic'},
						{title: 'Extra', name: 'extra'}
					]},
					{title: 'Textarea', name: 'textarea', tabs: [
						{title: 'Basic', name: 'basic'},
						{title: 'Extra', name: 'extra'}
					]},
					{title: 'Select', name: 'select', tabs: [
						{title: 'Basic', name: 'basic'},
						{title: 'Extra', name: 'extra'}
					]},
					{title: 'Image', name: 'image', tabs: [
						{title: 'Basic', name: 'basic'},
						{title: 'Extra', name: 'extra'}
					]},
					{title: 'Date', name: 'date', tabs: [
						{title: 'Basic', name: 'basic'},
						{title: 'Extra', name: 'extra'}
					]},
					{title: 'Currency', name: 'currency', tabs: [
						{title: 'Basic', name: 'basic'},
						{title: 'Extra', name: 'extra'}
					]},
					{title: 'List', name: 'list', tabs: [
						{title: 'Basic', name: 'basic'},
						{title: 'Extra', name: 'extra'}
					]},
					{title: 'Table', name: 'table', tabs: [
						{title: 'Basic', name: 'basic'},
						{title: 'Column', name: 'column'},
						{title: 'Footer', name: 'footer'},
						{title: 'Preview', name: 'preview'},
						{title: 'Invoice', name: 'invoice'},
						{title: 'Currency', name: 'currency'},
						{title: 'Extra', name: 'extra'}
					]}
				],
				form: {
					label: this.$t('new_field'),
					name: snakeCase(this.$t('new_field')),
					width: 50,
					model: '',
					format: this.$s('application.date_format'), // get from settins
					options: [''],
					list_model: [''],
					currency: {
						code: this.$s('currency.code'),
						precision: this.$s('currency.precision'),
						separator: this.$s('currency.decimal_separator'),
						thousands: this.$s('currency.thousands_separator'),
						placement: this.$s('currency.placement') // get from settings
					},
					thead: [],
					tbody: [],
					tfoot: [],
					colspan: {
						empty: 1,
						title: 1,
						value: 1
					},
					className: '',
					invoice: {
						sub_total: null,
						grand_total: null
					}
				}
			}
		},
		computed: {
			availableTypes() {
				return this.types.map((type) => {
					return type.name
				})
			}
		},
		mounted() {
			if(this.field) {
				this.$set(this.$data, 'form', JSON.parse(JSON.stringify(this.field)))

				const found = this.types.find((type) => {
					return this.field.type == type.name
				})

				this.selectType(found)
			}
		},
		methods: {
			selectType(type) {

				this.activeTab = 'field'
				this.activeType = type

				if(type.name === 'table') {
					this.form.width = 100
				}
			},
			addField() {
				const payload = this.getPayload()
				this.$emit(this.field ? 'update': 'add', {
					target: {
						value: payload
					}
				})
			},
			getPayload() {
				let payload = {}
				switch(this.activeType.name) {
					case 'text':
					case 'number':
					case 'textarea':
					case 'image':
						payload = {
							type: this.activeType.name,
							label: this.form.label,
							name: this.form.name,
							model: this.form.model,
							width: this.form.width
						}
						break;
					case 'date':
						payload = {
							type: this.activeType.name,
							label: this.form.label,
							name: this.form.name,
							model: this.form.model,
							width: this.form.width,
							format: this.form.format
						}
						break;
					case 'select':
						payload = {
							type: this.activeType.name,
							label: this.form.label,
							name: this.form.name,
							model: this.form.model,
							options: this.form.options,
							width: this.form.width
						}
						break;
					case 'table':
						payload = {
							type: this.activeType.name,
							label: this.form.label,
							name: this.form.name,
							thead: this.form.thead,
							tbody: this.form.tbody,
							tfoot: this.form.tfoot,
							width: this.form.width,
							currency: this.form.currency,
							colspan: this.form.colspan,
							class_name: this.form.class_name,
							invoice: this.form.invoice
						}
						break;
					case 'list':
						payload = {
							type: this.activeType.name,
							label: this.form.label,
							name: this.form.name,
							list_model: this.form.list_model,
							width: this.form.width,
							class_name: this.form.class_name
						}
						break;
					case 'currency':
						payload = {
							type: this.activeType.name,
							label: this.form.label,
							name: this.form.name,
							model: this.form.model,
							width: this.form.width,
							currency: this.form.currency
						}
						break;
				}

				return payload
			}
		}
	}
</script>