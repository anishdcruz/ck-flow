<template>
	<div>
		<x-table class="table-form">
		  <x-thead>
		    <x-tr>
		      <x-td v-for="th in theadActive" :key="th.name"
		        type="th" :size="th.width" v-if="th.type !== 'hidden'"
		        >{{th.title}}</x-td>
		      <th></th>
		    </x-tr>
		  </x-thead>
		  <x-tbody>
		  	<template v-for="(row, ix) in tbodyActive">
		  	  <x-tr>
		  	  	<template v-for="(col, cx) in theadActive">
		  	  		<td v-if="col.type === 'select'">
		  	  		  <select class="form-input" v-model="row[col.name]">
		  	  		    <option v-for="option in col.options">{{option}}</option>
		  	  		  </select>
		  	  		</td>
		  	  		<td v-if="col.type === 'text'">
		  	  		  <input type="text" class="form-input" v-model="row[col.name]">
		  	  		</td>
		  	  		<td v-if="col.type === 'item_lookup'">
		  	  			<x-typeahead-table :value="row[col.name]" url="items" :name="col.val"
  								:columns="['code', 'description', 'unit_price']"
  							  @input="onItem(row, col, $event)">
  							</x-typeahead-table>
		  	  		</td>
		  	  		<td v-if="col.type === 'number'">
		  	  		  <input type="number" class="form-input" v-model="row[col.name]">
		  	  		</td>
		  	  		<td v-if="col.type === 'date'">
		  	  		  <input type="date" class="form-input" v-model="row[col.name]">
		  	  		</td>
		  	  		<td v-if="col.type === 'currency'">
		  	  		  <input type="number" class="form-input" v-model="row[col.name]">
		  	  		</td>
		  	  		<td v-if="col.type === 'image'">
		  	  		  <field-image type="form-image-table" v-model="row[col.name]"></field-image>
		  	  		</td>
		  	  		<td v-if="col.type === 'textarea'">
		  	  		  <textarea class="form-input form-textarea" v-model="row[col.name]"></textarea>
		  	  		</td>
		  	  		<template v-if="col.type === 'hidden' && compute(col, row)">
		  	  		</template>
		  	  		<td v-if="col.type === 'computed_currency'">
		  	  			<span class="form-input">{{compute(col, row) | formatCurrency(field.currency)}}</span>
		  	  		</td>
		  	  	</template>
		  	  	<td>
		  	  		<span class="form-list-remove">
		  	  		  <i class="icon icon-trash-a" @click="field.tbody.splice(ix, 1)"></i>
		  	  		</span>
		  	  	</td>
		  	  </x-tr>
		  	</template>
		  </x-tbody>
		  <x-tfoot>
		  	<x-tr>
		  		<x-td :colspan="thActive.length">
		  			<div class="table-form-ctrl">
		  				<x-button icon="plus" size="sm" @click="addRow">{{$t('add')}}</x-button>
		  			</div>
		  		</x-td>
		  	</x-tr>
		  	<template  v-if="tfootActive.length > 0">
			  	<x-tr v-if="edit && field.colspan">
			  		<td :colspan="field.colspan.empty">
			  			<span class="table-td">
			  					<span>{{$t('colspan')}}</span>
			  					<input type="number" class="form-input form-input-sm"
			  					v-model="field.colspan.empty">:
			  			</span>
			  		</td>
			  		<td :colspan="field.colspan.title">
			  			<span class="table-td">
			  					<span>{{$t('colspan')}}</span>
			  					<input type="number" class="form-input form-input-sm"
			  					v-model="field.colspan.title">:
			  			</span>
			  		</td>
			  		<td :colspan="field.colspan.value">
			  			<span class="table-td">
			  					<span>{{$t('colspan')}}</span>
			  					<input type="number" class="form-input form-input-sm"
			  					v-model="field.colspan.value">:
			  			</span>
			  		</td>
			  	</x-tr>
			  	<template v-for="(vl, vx) in tfootActive">
			  		<template v-if="vl.type === 'hidden' && computeTotal(vl, tfootActive)">
			  		</template>
			  		<tr v-if="vl.type === 'aggregate'">
			  			<td :colspan="field.colspan.empty"></td>
			  			<td :colspan="field.colspan.title" class="table-foot">
			  				<span class="table-td">{{vl.title}}</span>
			  			</td>
			  			<td :colspan="field.colspan.value" class="table-foot">
			  				<span class="table-td">{{computeAggregate(vl, tbodyActive)}}</span>
			  			</td>
			  		</tr>
			  		<tr v-if="vl.type === 'aggregate_currency'">
			  			<td :colspan="field.colspan.empty"></td>
			  			<td :colspan="field.colspan.title" class="table-foot">
			  				<span class="table-td">{{vl.title}}</span>
			  			</td>
			  			<td :colspan="field.colspan.value" class="table-foot">
			  				<span class="table-td">{{computeAggregate(vl, tbodyActive) | formatCurrency(field.currency)}}</span>
			  			</td>
			  		</tr>
			  		<tr v-if="vl.type === 'input_currency'">
			  			<td :colspan="field.colspan.empty"></td>
			  			<td :colspan="field.colspan.title" class="table-foot">
			  				<span class="table-td">
			  					<span>{{vl.title}}</span>
			  					<input type="text" class="form-input form-input-sm" v-model="vl.model">
			  				</span>
			  			</td>
			  			<td :colspan="field.colspan.value" class="table-foot">
			  				<span class="table-td">{{vl.model | formatCurrency(field.currency)}}</span>
			  				</td>
			  		</tr>
			  		<tr v-if="vl.type === 'tax'">
			  			<td :colspan="field.colspan.empty"></td>
			  			<td :colspan="field.colspan.title" class="table-foot">
			  				<span class="table-td">
			  					<span>{{vl.title}}</span>
			  					<input type="text" class="form-input form-input-sm" v-model="vl.percent_model"> %
			  				</span>
			  			</td>
			  			<td :colspan="field.colspan.value" class="table-foot">
			  				<span class="table-td">{{computeTax(vl, tfootActive) | formatCurrency(field.currency)}}</span>
			  			</td>
			  		</tr>
			  		<tr v-if="vl.type === 'computed_currency'">
			  			<td :colspan="field.colspan.empty"></td>
			  			<td :colspan="field.colspan.title" class="table-foot">
			  				<span class="table-td">{{vl.title}}</span>
			  			</td>
			  			<td :colspan="field.colspan.value" class="table-foot">
			  				<span class="table-td">{{computeTotal(vl, tfootActive) | formatCurrency(field.currency)}}</span>
			  			</td>
			  		</tr>
			  		<tr v-if="vl.type === 'computed'">
			  			<td :colspan="field.colspan.empty"></td>
			  			<td :colspan="field.colspan.title" class="table-foot">
			  				<span class="table-td">{{vl.title}}</span>
			  			</td>
			  			<td :colspan="field.colspan.value" class="table-foot">
			  				<span class="table-td">{{computeTotal(vl, tfootActive)}}</span>
			  			</td>
			  		</tr>
			  	</template>
			  </template>
		  </x-tfoot>
		</x-table>
	</div>
</template>
<script>
	import FieldImage from '@js/partials/FieldImage.vue'
	// import ItemTypeahead from '@/js/partials/ItemTypeahead.vue'
	import { get } from 'lodash'
	export default {
		components: {
			FieldImage,
			// ItemTypeahead
		},
		props: {
			field: Object,
			edit: {
				type: Boolean,
				default: false
			}
		},
		data() {
			return {
				itemColumns: {
					item_code: this.$t('item_code'),
					description: this.$t('description'),
					unit_price: this.$t('unit_price')}
			}
		},
		computed: {
			tbodyActive() {
				return this.field.tbody
			},
			theadActive() {
				return this.field.thead
			},
			thActive() {
				return this.field.thead.filter((th) => {
					return th.type !== 'hidden'
				})
			},
			tfootActive() {
				return this.field.tfoot || []
			},
			getColumn() {
				return (key, thead) => {
					return thead.find((item) => {
						return item.name === key
					})
				}
			},
			computeTax() {
				return (f, tfoot) => {
					const subTotal = this.getColumn(f.sub_total, tfoot).model
					const result = Number(subTotal) * Number(f.percent_model) / 100
					f.model = result
					return result
				}
			},
			computeAggregate() {
				return (f, tbody) => {
					const result = tbody.reduce((carry, item) => {
						return this.calculateFormula(f.formula, carry, item[f.tbody_column])
					}, this.getFormula(f.formula))
					f.model = result
					return result
				}
			},
			computeTotal() {
				return (f, tfoot) => {
					const result = f.columns.reduce((carry, col) => {
						const t = this.getColumn(col, tfoot).model

						return this.calculateFormula(f.formula, carry, t)
					}, this.getFormula(f.formula))
					f.model = result
					return result
				}
			},
			compute() {
				return (f, row) => {
					const result = f.columns.reduce((carry, col) => {
						return this.calculateFormula(f.formula, carry, row[col])
					}, this.getFormula(f.formula))
					row[f.name] = result
					return result
				}
			}
		},
		methods: {
			onItem(row, col, item) {
				row[col.name] = item[col.val]
				// field_map
				if(col.field_map) {
					col.field_map.forEach((field) => {
						row[field.col] = _.get(item, field.item, '');
					})
				}
			},
			addRow() {
				const f = {}
				this.field.thead.forEach((c) => {

					f[c.name] = c.default || ''
				})

				this.field.tbody.push(f)
			},
			calculateFormula(type, carry, num) {
				if(type === 'sum') {
					return carry + Number(num)
				} else if(type === 'difference') {
					return Number(num) - carry
				} else if(type === 'product') {
					return carry * Number(num)
				}
			},
			getFormula(type) {
				const f = {
					'sum': 0,
					'difference': 0,
					'product': 1
				}
				return f[type]
			},
		}
	}
</script>