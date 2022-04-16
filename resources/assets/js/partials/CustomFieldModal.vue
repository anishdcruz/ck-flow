<template>
	<x-modal width="450" @cancel="$emit('cancel')">
		<div slot="title">
			<div v-if="field">
				<span>{{$t('edit')}} {{$t('custom_field')}}</span>
			</div>
			<div v-else>{{$t('add')}} {{$t('custom_field')}}</div>
		</div>
		<div>
			<x-form-group :label="$t('field_type')">
				<select class="form-input" :disabled="field" :value="form.type" @change="onSelect">
					<option :value="type" :selected="form.type === type"
						v-for="type in types">{{$t(type)}}</option>
				</select>
			</x-form-group>
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
			<div class="form-group">
				<label class="form-label">
				  {{$t('default_value')}}
				</label>
				<x-input v-if="activeType ==='text'" v-model="form.model"></x-input>
				<x-input v-if="activeType ==='number'" type="number" v-model="form.model"></x-input>
				<x-input v-if="activeType ==='date'" type="date" v-model="form.model"></x-input>
				<x-input v-if="activeType ==='currency'" type="number" v-model="form.model"></x-input>
				<x-textarea v-if="activeType ==='textarea'" v-model="form.model"></x-textarea>
				<field-image v-if="activeType === 'image'" v-model="form.model"></field-image>
				<select class="form-input" v-if="activeType === 'select'" v-model="form.model">
					<option v-for="o in form.options" :value="o">{{o}}</option>
				</select>
			</div>
			<div v-if="activeType === 'currency'">
				<x-row>
		  		<x-form-group col="12" v-model="form.currency.code" :label="$t('currency_code')"></x-form-group>
		  		<x-form-group col="12" v-model="form.currency.precision" :label="$t('precision')"></x-form-group>
				</x-row>
				<x-row>
					<x-form-group col="8" v-model="form.currency.separator" :label="$t('separator')"></x-form-group>
					<x-form-group col="8" v-model="form.currency.thousands" :label="$t('thousands_separator')"></x-form-group>
					<x-col span="8">
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
			<div class="form-group" v-if="activeType === 'select'">
				<label class="form-label">
				  {{$t('options')}}
				</label>
				<div class="form-list" v-for="(m, i) in form.options">
				  <input type="text" class="form-input" :placeholder="`${$t('option')} ${i + 1}`" v-model="form.options[i]">
				  <span class="form-list-remove" @click="form.options.splice(i, 1)">
				  	<i class="icon icon-close"></i>
				  </span>
				</div>
				<x-button type="success" size="sm" icon="plus"
					@click="form.options.push('')">{{$t('add')}}</x-button>
			</div>
			<div class="form-group" v-if="activeType === 'date'">
				<label class="form-label">
				  {{$t('date_format')}}
				</label>
				<select class="form-input" v-model="form.format">
					<option v-for="format in formats" :value="format">{{format}}</option>
				</select>
			</div>
			<div class="form-group">
				<label class="form-label">
				  {{$t('width')}}
				</label>
				<x-width v-model="form.width" size="lg"></x-width>
			</div>
		</div>
		<template slot="footer">
			<div></div>
			<div>
				<x-button @click="$emit('cancel')">{{$t('cancel')}}</x-button>
				<x-button type="primary" @click="addField">
					{{field ? this.$t('ok') : this.$t('add')}}
				</x-button>
			</div>
		</template>
	</x-modal>
</template>
<script>
	import { snakeCase } from 'lodash'
	import FieldImage from '@js/partials/FieldImage.vue'
	export default {
		components: { FieldImage },
		props: {
			field: {
				type: Object,
				default: null
			}
		},
		data() {
			return {
				edit: false,
				activeType: 'text',
				activeTab: 'menu',
				types: [
					'text',
					'number',
					'textarea',
					'select',
					'image',
					'date',
					'currency'
				],
				formats: [
					'd-m-Y',
					'Y-m-d',
					'd-M-Y',
					'Y-M-d',
					'd/m/Y',
					'Y/m/d'
				],
				form: {
					type: 'text',
					label: this.$t('new_field'),
					name: snakeCase(this.$t('new_field')),
					width: 33,
					model: '',
					format: this.$s('application.date_format'), // get from settins
					options: [''],
					currency: {
						code: this.$s('currency.code'),
						precision: this.$s('currency.precision'),
						separator: this.$s('currency.decimal_separator'),
						thousands: this.$s('currency.thousands_separator'),
						placement: this.$s('currency.placement') // get from settings
					}
				}
			}
		},
		mounted() {
			if(this.field) {
				this.$set(this.$data, 'form', JSON.parse(JSON.stringify(this.field)))

				const found = this.types.find((type) => {
					return this.field.type == type
				})

				this.selectType(found)
			}
		},
		methods: {
			onSelect(e) {
				this.form.type = e.target.value
				this.selectType(e.target.value)
			},
			handleTitle(value) {
				this.form.label = value
				this.form.name = snakeCase(value)
			},
			selectType(type) {
				this.activeType = type
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
				switch(this.activeType) {
					case 'text':
					case 'number':
					case 'textarea':
					case 'image':
						payload = {
							type: this.activeType,
							label: this.form.label,
							name: this.form.name,
							model: this.form.model,
							width: this.form.width
						}
						break;
					case 'date':
						payload = {
							type: this.activeType,
							label: this.form.label,
							name: this.form.name,
							model: this.form.model,
							width: this.form.width,
							format: this.form.format
						}
						break;
					case 'select':
						payload = {
							type: this.activeType,
							label: this.form.label,
							name: this.form.name,
							model: this.form.model,
							options: this.form.options,
							width: this.form.width
						}
						break;
					case 'currency':
						payload = {
							type: this.activeType,
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