<template>
	<div>
		<field-tab :tabs="tabs">
			<div slot="basic">
				<x-row>
					<x-col span="12">
						<div class="form-group">
							<label class="form-label">
							  {{$t('field_label')}}
							</label>
							<x-input :value="form.label" @input="handleTitle"></x-input>
						</div>
					</x-col>
					<x-col span="12">
						<div class="form-group">
							<label class="form-label">
							  {{$t('field_name')}}
							</label>
							<x-input v-model="form.name" disabled></x-input>
						</div>
					</x-col>
				</x-row>
			</div>
			<div slot="column">
				<x-row>
					<x-col span="8">
						<ul class="column-list">
							<li>
								<span class="column-control">
									<strong>{{$t('columns')}}</strong>
									<x-button type="success" size="sm" @click="addNewColumn">{{$t('add')}}</x-button>
								</span>
							</li>
							<draggable v-model="form.thead"  @start="drag=true" @end="drag=false">
			        <li v-for="(th, i) in form.thead">
			        	<a @click.stop="changeColumn(i)"
			        		:class="['column-link', active === i ? 'column-active' : '']">
			        		<i class="column-icon icon icon-document-text"></i>
			        		<span class="column-text">
		                {{th.title}}
		              </span>
		              <i class="column-remove icon icon-trash-a" @click.stop="remove(th, i)"></i>
			        	</a>
			        </li>
			      </draggable>
			        <li>
			        	<small>24 / {{lineWidth}}</small>
			        </li>
						</ul>
					</x-col>
					<x-col span="16" v-if="form.thead[active]">
						<x-row>
							<x-form-group col="14" :value="form.thead[active].title"
								@input="handleColumnTitle(form.thead[active], $event)" :label="$t('column_title')"></x-form-group>
							<x-form-group col="10" :label="$t('column_type')">
								<select class="form-input" v-model="form.thead[active].type">
									<option v-for="type in columnTypes" :value="type.name">{{type.title}}</option>
								</select>
							</x-form-group>
						</x-row>
						<x-row v-if="form.thead[active].type !=='hidden'">
							<x-form-group col="8" :label="$t('width')">
								<select class="form-input" v-model="form.thead[active].width">
									<option v-for="x in 24" :value="x">{{x}}</option>
								</select>
							</x-form-group>
							<x-form-group col="8" :label="$t('align')">
								<select class="form-input" v-model="form.thead[active].align">
									<option v-for="x in ['left', 'center', 'right']" :value="x">{{$t(x)}}</option>
								</select>
							</x-form-group>
						</x-row>
						<hr>
						<x-row v-if="form.thead[active].type === 'item_lookup'">
							<x-col span="12">
								<div class="form-group">
									<label class="form-label">
									  {{$t('value')}}
									</label>
									<select class="table-form-control" v-model="form.thead[active].val">
										<option v-for="(value, key) in shared.itemVariables" :value="key">
										{{value}}
										</option>
									</select>
								</div>
							</x-col>
							<x-col span="12">
								<div class="form-group">
									<label class="form-label">
										{{$t('auto_field_mapping')}}
									</label>
									<x-row>
										<x-col span="12"><span>{{$t('from')}}</span></x-col>
										<x-col span="12"><span>{{$t('to')}}</span></x-col>
									</x-row>
									<br>
									<div class="form-list" v-for="(m, i) in form.thead[active].field_map">
									  <select class="table-form-control" v-model="m.item">
									  	<option v-for="(value, key) in shared.itemVariables" :value="key">
									  	{{value}}
									  	</option>
									  </select>
									  <select class="table-form-control" v-model="m.col">
									  	<option v-for="f in columnsExcept(form.thead[active], form.thead)" :value="f">{{f}}</option>
									  </select>
									  <span class="form-list-remove" @click="form.thead[active].field_map.splice(i, 1)">
									  	<i class="icon icon-trash-a"></i>
									  </span>
									</div>
									<x-button type="success" size="sm" icon="plus"
										@click="form.thead[active].field_map.push({item: 'code', col: ''})">{{$t('add')}}</x-button>
								</div>
							</x-col>
						</x-row>
						<x-row v-if="form.thead[active].type === 'select'">
							<x-col span="12">
								<div class="form-group">
									<label class="form-label">
									  {{$t('options')}}
									</label>
									<div class="form-list" v-for="(m, i) in form.thead[active].options">
									  <input type="text" class="form-input" :placeholder="`Option ${i + 1}`" v-model="form.thead[active].options[i]">
									  <span class="form-list-remove" @click="form.thead[active].options.splice(i, 1)">
									  	<i class="icon icon-trash-a"></i>
									  </span>
									</div>
									<x-button type="success" size="sm" icon="plus"
										@click="form.thead[active].options.push('')">{{$t('add')}}</x-button>
								</div>
							</x-col>
							<x-col span="12">
								<div class="form-group">
									<label class="form-label">
									  {{$t('default_option')}}
									</label>
									<select class="form-input" v-model="form.thead[active].default">
										<option v-for="o in form.thead[active].options" :value="o">{{o}}</option>
									</select>
								</div>
							</x-col>
						</x-row>
						<x-row v-else>
							<x-col span="12">
								<div class="form-group">
									<label class="form-label">
									  {{$t('default_value')}}
									</label>
									<input v-if="['text', 'date', 'number'].includes(form.thead[active].type)"
										:type="form.thead[active].type" class="form-input" v-model="form.thead[active].default">
									<input v-else-if="['currency', 'computed_currency', 'computed', 'hidden'].includes(form.thead[active].type)"
										type="number" class="form-input" v-model="form.thead[active].default">
									<textarea v-else-if="form.thead[active].type === 'textarea'" class="form-input" v-model="form.thead[active].default"></textarea>
									<field-image v-model="form.thead[active].default" v-else-if="form.thead[active].type === 'image'"></field-image>
								</div>
							</x-col>
							<x-col span="6" v-if="form.thead[active].type === 'date'">
								<div class="form-group">
									<label class="form-label">
									  {{$t('date_format')}}
									</label>
									<select class="form-input" v-model="form.thead[active].format">
										<option v-for="format in formats" :value="format">{{format}}</option>
									</select>
								</div>
							</x-col>
						</x-row>
						<x-row v-if="['computed_currency', 'computed', 'hidden'].includes(form.thead[active].type)">
							<x-col span="6">
								<div class="form-group">
									<label class="form-label">
										{{$t('formula')}}
									</label>
									<select class="form-input" v-model="form.thead[active].formula">
										<option v-for="f in formulas" :value="f">{{$t(f)}}</option>
									</select>
								</div>
							</x-col>
							<x-col span="16">
								<div class="form-group">
									<label class="form-label">{{$t('columns')}}</label>
									<x-simple-select v-model="form.thead[active].columns" :options="columnsExcept(form.thead[active], form.thead)" multiple></x-simple-select>
								</div>
							</x-col>
						</x-row>
					</x-col>
				</x-row>
			</div>
			<div slot="footer">
				<x-row>
					<x-col span="8">
						<ul class="column-list">
							<li>
								<span class="column-control">
									<strong>{{$t('footer')}}</strong>
									<x-button type="success" size="sm" @click="addNewFooter">{{$t('add')}}</x-button>
								</span>
							</li>
							<draggable v-model="form.tfoot"  @start="drag=true" @end="drag=false">
			        <li v-for="(th, i) in form.tfoot">
			        	<a @click.stop="changeFoot(i)"
			        		:class="['column-link', af === i ? 'column-active' : '']">
			        		<i class="column-icon icon icon-document-text"></i>
			        		<span class="column-text">
		                {{th.title}}
		              </span>
		              <i class="column-remove icon icon-trash-a" @click.stop="form.tfoot.splice(i, 1)"></i>
			        	</a>
			        </li>
			      </draggable>
						</ul>
					</x-col>
					<x-col span="16" v-if="form.tfoot[af]">
						<x-row>
							<x-form-group col="14" :value="form.tfoot[af].title"
								@input="handleColumnTitle(form.tfoot[af], $event)" :label="$t('column_title')"></x-form-group>
							<x-form-group col="10" :label="$t('column_type')">
								<select class="form-input" v-model="form.tfoot[af].type">
									<option v-for="type in footerTypes" :value="type.name">{{type.title}}</option>
								</select>
							</x-form-group>
						</x-row>
						<x-row v-if="form.tfoot[af].type !=='hidden'">
							<x-form-group col="8" :label="$t('align')">
								<select class="form-input" v-model="form.tfoot[af].align">
									<option v-for="x in ['left', 'center', 'right']" :value="x">{{$t(x)}}</option>
								</select>
							</x-form-group>
						</x-row>
						<hr>
						<x-row>
							<x-col span="12">
								<div class="form-group">
									<label class="form-label">
									  {{$t('default_value')}}
									</label>
									<input type="number" class="form-input" v-model="form.tfoot[af].model">
								</div>
							</x-col>
						</x-row>
						<x-row v-if="['computed_currency', 'computed', 'hidden'].includes(form.tfoot[af].type)">
							<x-col span="6">
								<div class="form-group">
									<label class="form-label">
										{{$t('formula')}}
									</label>
									<select class="form-input" v-model="form.tfoot[af].formula">
										<option v-for="f in formulas" :value="f">{{f}}</option>
									</select>
								</div>
							</x-col>
							<x-col span="16">
								<div class="form-group">
									<label class="form-label">{{$t('columns')}}</label>
									<x-simple-select v-model="form.tfoot[af].columns" :options="columnsExcept(form.tfoot[af], form.tfoot)" multiple></x-simple-select>
								</div>
							</x-col>
						</x-row>
						<x-row v-else-if="['aggregate', 'aggregate_currency'].includes(form.tfoot[af].type)">
							<x-col span="6">
								<div class="form-group">
									<label class="form-label">
										{{$t('formula')}}
									</label>
									<select class="form-input" v-model="form.tfoot[af].formula">
										<option v-for="f in formulas" :value="f">{{f}}</option>
									</select>
								</div>
							</x-col>
							<x-col span="16">
								<div class="form-group">
									<label class="form-label">{{$t('column')}}</label>
									<select class="form-input" v-model="form.tfoot[af].tbody_column">
										<option v-for="f in theadColumns" :value="f">{{f}}</option>
									</select>
								</div>
							</x-col>
						</x-row>
						<x-row v-else-if="['tax'].includes(form.tfoot[af].type)">
							<x-col span="6">
								<div class="form-group">
									<label class="form-label">
									  {{$t('percentage')}}
									</label>
									<input type="number" class="form-input" v-model="form.tfoot[af].percent_model">
								</div>
							</x-col>
							<x-col span="8">
								<div class="form-group">
									<label class="form-label">{{$t('select_sub_total')}}</label>
									<select class="form-input" v-model="form.tfoot[af].sub_total">
										<option v-for="f in columnsExcept(form.tfoot[af], form.tfoot)" :value="f">{{f}}</option>
									</select>
								</div>
							</x-col>
						</x-row>
					</x-col>
				</x-row>
			</div>
			<div slot="preview">
				<field-table edit :field="form"></field-table>
			</div>
			<div slot="currency">
				<x-row>
		  		<x-form-group col="6" v-model="form.currency.code" label="Currency Code"></x-form-group>
		  		<x-form-group col="6" v-model="form.currency.precision" label="Precision"></x-form-group>
				</x-row>
				<x-row>
					<x-form-group col="6" v-model="form.currency.separator" label="Separator"></x-form-group>
					<x-form-group col="6" v-model="form.currency.thousands" :label="$t('thousands_separator')"></x-form-group>
					<x-col span="6">
						<div class="form-group">
							<label class="form-label">{{$t('placement')}}</label>
							<select class="form-input" v-model="form.currency.placement">
								<option value="before">{{$t('before')}}</option>
								<option value="after">{{$t('after')}}</option>
							</select>
						</div>
					</x-col>
				</x-row>
			</div>
			<div slot="invoice">
				<x-row>
		  		<x-form-group col="8" :label="$t('sub_total')">
		  			<select class="form-input" v-model="form.invoice.sub_total">
		  				<option v-for="f in form.tfoot" :value="f.name">{{f.title}}</option>
		  			</select>
		  		</x-form-group>
		  		<x-form-group col="8" :label="$t('grand_total')">
		  			<select class="form-input" v-model="form.invoice.grand_total">
		  				<option v-for="f in form.tfoot" :value="f.name">{{f.title}}</option>
		  			</select>
		  		</x-form-group>
				</x-row>
			</div>
			<div slot="extra">
				<x-row>
					<x-col span="4">
						<div class="form-group">
							<label class="form-label">
							  {{$t('width')}}
							</label>
							<x-width v-model="form.width" size="lg"></x-width>
						</div>
					</x-col>
					<x-col span="8">
						<div class="form-group">
							<label class="form-label">
							  {{$t('custom_class_name')}}
							</label>
							<x-input type="text" v-model="form.class_name"></x-input>
						</div>
					</x-col>
				</x-row>
			</div>
		</field-tab>
	</div>
</template>
<script>
	import FieldTab from '@js/components/tabs/FieldTab.vue'
	import FieldImage from '@js/partials/FieldImage.vue'
	import FieldTable from '@js/partials/FieldTable.vue'
	import shared from '@js/shared'
	import { snakeCase } from 'lodash'
	import Draggable from 'vuedraggable'
	export default {
		name: 'TableType',
		props: {
			tabs: Array,
			form: Object,
			type: {
				default: 'text'
			}
		},
		components: {
			FieldTab, FieldImage,
			FieldTable, Draggable
		},
		computed: {
			lineWidth() {
				return this.form.thead.reduce((cr, th) => {
					return cr + th.width
				}, 0)
			},
			theadColumns() {
				return this.form.thead.map((col) => {
					return col.name
				})
			},
			columnsExcept() {
				return (th, collection) => {
					const result = collection.map((col) => {
						return col.name
					}).filter((col) => {

						if(col !== th.name) {
							return col
						}
					})

						return result
				}
			}
		},
		data() {
			return {
				active: 0,
				af: 0,
				shared: shared,
				formulas: [
					'sum',
					'difference',
					'product',
				],
				formats: [
					'd-m-Y',
					'Y-m-d',
					'd-M-Y',
					'Y-M-d',
					'd/m/Y',
					'Y/m/d'
				],
				columnTypes: [
					{title: this.$t('text'), name: 'text'},
					{title: this.$t('number'), name: 'number'},
					{title: this.$t('textarea'), name: 'textarea'},
					{title: this.$t('item_lookup'), name: 'item_lookup'},
					{title: this.$t('select'), name: 'select'},
					{title: this.$t('image'), name: 'image'},
					{title: this.$t('date'), name: 'date'},
					{title: this.$t('currency'), name: 'currency'},
					{title: this.$t('computed_currency'), name: 'computed_currency'},
					{title: this.$t('computed'), name: 'computed'},
					{title: this.$t('hidden'), name: 'hidden'}
				],
				footerTypes: [
					{title: this.$t('aggregate'), name: 'aggregate'},
					{title: this.$t('aggregate_currency'), name: 'aggregate_currency'},
					{title: this.$t('tax'), name: 'tax'},
					{title: this.$t('input_currency'), name: 'input_currency'},
					{title: this.$t('computed_currency'), name: 'computed_currency'},
					{title: this.$t('computed'), name: 'computed'},
					{title: this.$t('hidden'), name: 'hidden'}
				]
			}
		},
		methods: {
			changeColumn(i) {
				this.active = i
			},
			changeFoot(i) {
				this.af = i
			},
			addNewColumn() {
				const nI = this.form.thead.length + 1
				const d = this.$t('new_column')
				this.form.thead.push({
					title: `${d} ${nI}`,
					name: `${snakeCase(d)}${nI}`,
					width: 4,
					align: 'left',
					type: 'text',
					default: '',
					options: [''],
					format: 'Y-m-d',
					formula: 'sum',
					columns: [],
					val: 'code',
					field_map: []
				})
				this.active = nI - 1
			},
			addNewFooter() {
				const nI = this.form.tfoot.length + 1
				const d = this.$t('new_row')
				this.form.tfoot.push({
					title: `${d} ${nI}`,
					name: `${snakeCase(d)}${nI}`,
					align: 'right',
					type: 'aggregate_currency',
					model: '',
					percent_model: 0,
					sub_total: '',
					tbody_column: '',
					formula: 'sum',
					columns: []
				})
				this.af = nI - 1
			},
			handleColumnTitle(th, v) {
				th.title = v
				th.name = snakeCase(th.title)
			},
			handleTitle(value) {
				this.form.label = value
				this.form.name = snakeCase(value)
			},
			remove(th, i) {
				const r = confirm(this.$t('are_you_sure'))
				if(r != true) { return }
				this.form.thead.splice(i, 1)
			}
		}
	}
</script>