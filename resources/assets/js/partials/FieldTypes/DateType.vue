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
							<x-input type="date" v-model="form.model"></x-input>
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
				<hr>
				<x-row>
					<x-col span="6">
						<div class="form-group">
							<label class="form-label">
							  {{$t('date_format')}}
							</label>
							<select class="form-input" v-model="form.format">
								<option v-for="format in formats" :value="format">{{format}}</option>
							</select>
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
			</div>
		</field-tab>
	</div>
</template>
<script>
	import FieldTab from '@js/components/tabs/FieldTab.vue'
	import { snakeCase } from 'lodash'
	export default {
		props: {
			tabs: Array,
			form: Object
		},
		data() {
			return {
				formats: [
					'd-m-Y',
					'Y-m-d',
					'd-M-Y',
					'Y-M-d',
					'd/m/Y',
					'Y/m/d'
				]
			}
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