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
						<div class="form-group">
							<label class="form-label">
							  {{$t('default_value')}}
							</label>
							<x-input type="number" v-model="form.model"></x-input>
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
			<div slot="extra">
				<x-row>
					<x-col span="12">
						<div class="form-group">
							<label class="form-label">
							  {{$t('width')}}
							</label>
							<x-width v-model="form.width" size="lg"></x-width>
						</div>
					</x-col>
				</x-row>
					<hr>
					<x-row>
			  		<x-form-group col="6" v-model="form.currency.code" :label="$t('currency_code')"></x-form-group>
			  		<x-form-group col="6" v-model="form.currency.precision" :label="$t('precision')"></x-form-group>
					</x-row>
					<x-row>
						<x-form-group col="6" v-model="form.currency.separator" :label="$t('separator')"></x-form-group>
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
		</field-tab>
	</div>
</template>
<script>
	import FieldTab from '@js/components/tabs/FieldTab.vue'
	import { snakeCase } from 'lodash'
	export default {
		name: 'TextType',
		props: {
			tabs: Array,
			form: Object
		},
		components: { FieldTab },
		methods: {
			handleTitle(value) {
				this.form.label = value
				this.form.name = snakeCase(value)
			},
		}
	}
</script>