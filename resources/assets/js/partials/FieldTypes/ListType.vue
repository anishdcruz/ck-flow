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
				<hr>
				<x-row>
					<x-col span="16">
						<div class="form-group">
							<label class="form-label">
							  {{$t('list_items')}}
							</label>
							<div class="form-list" v-for="(m, i) in form.list_model">
							  <input type="text" class="form-input" :placeholder="`${$t('item')} ${i + 1}`" v-model="form.list_model[i]">
							  <span class="form-list-remove" @click="form.list_model.splice(i, 1)">
							  	<i class="icon icon-trash-a"></i>
							  </span>
							</div>
							<x-button type="success" size="sm" icon="plus"
								@click="form.list_model.push('')">{{$t('add')}}</x-button>
						</div>
					</x-col>
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