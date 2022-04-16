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
							  {{$t('default_option')}}
							</label>
							<select class="form-input" v-model="form.model">
								<option v-for="o in form.options" :value="o">{{o}}</option>
							</select>
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
					<x-col span="16">
						<div class="form-group">
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