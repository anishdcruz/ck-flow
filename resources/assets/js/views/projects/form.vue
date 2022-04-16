<template>
	<div class="content-inner" v-if="show">
		<x-panel padding margin>
			<div slot="title">
				{{$t(mode)}} {{$t('project')}}
			</div>
			<x-row line>
				<x-form-group col="8" v-model="form.number" :label="$t('number')" disabled></x-form-group>
				<x-form-group col="8" :label="$t('contact')" :errors="errors.contact_id">
					<x-typeahead-table :value="form.contact" url="contacts" name="name"
						:columns="['number', 'name']"
					  @input="value => { form.contact_id = value.id; form.contact = value }">
					</x-typeahead-table>
				</x-form-group>
				<x-form-group col="8" :label="$t('category')" :errors="errors.category_id">
					<x-tag-input :value="form.category" resource="project_categories" column="name" name="name"
					    @input="value => { form.category_id = value.id; form.category = value }">
					</x-tag-input>
				</x-form-group>
				<x-form-group col="8" v-model="form.title" :label="$t('title')" :errors="errors.title"></x-form-group>
				<x-form-group source="textarea" col="8" v-model="form.description" :label="$t('description')" :errors="errors.description"></x-form-group>
				<x-form-group col="8" :label="$t('proposal')" :errors="errors.proposal_id" optional>
					<x-tag-input :value="form.proposal" :params="{contact_id: form.contact_id}"
							resource="proposals" column="number" name="number"
					    @input="value => { form.proposal_id = value.id; form.proposal = value }">
					</x-tag-input>
				</x-form-group>
			</x-row>
			<x-row line>
				<x-form-group col="8" type="date" v-model="form.start_date" :label="$t('start_date')" :errors="errors.start_date"></x-form-group>
				<x-form-group col="8" type="date" v-model="form.estimated_finish_date" :label="$t('estimated_finish_date')" :errors="errors.estimated_finish_date"></x-form-group>
				<x-form-group col="8" type="date" v-model="form.deadline_date" :label="$t('deadline_date')" :errors="errors.deadline_date" optional></x-form-group>
			</x-row>
			<x-row line>
				<x-form-group col="8" type="date" v-model="form.actual_start_date" :label="$t('actual_start_date')" :errors="errors.actual_start_date" optional></x-form-group>
				<x-form-group col="8" type="date" v-model="form.actual_end_date" :label="$t('actual_end_date')" :errors="errors.actual_end_date" optional></x-form-group>
			</x-row>
			<x-row line>
				<x-form-group col="8" type="number" v-model="form.estimated_cost" :label="$t('estimated_cost')" :errors="errors.estimated_cost"></x-form-group>
			</x-row>
			<div class="fields row-line" v-if="form && form.custom_fields.length > 0">
	      <div :class="[`field field-${field.width}`]" v-for="(field, fIndex) in form.custom_fields">
	        <field-item :editable="false" page="contacts" :field="field"></field-item>
	      </div>
			</div>
	  	<div slot="footer" class="flex flex-end">
	  		<div>
	  			<x-button @click="cancel" :disabled="isSaving">{{$t('cancel')}}</x-button>
	  			<x-button @click="save" type="primary" :loading="isSaving">{{$t('save')}}</x-button>
	  		</div>
	  	</div>
		</x-panel>
	</div>
</template>
<script>
	import { formable } from '@js/lib/mixins'
	import FieldItem from '@js/partials/CustomFieldItem.vue'
	export default {
		mixins: [ formable ],
		components: { FieldItem },
		data() {
			return {
				redirect: 'projects',
				form: {
					custom_fields: [],
					contact: {},
					opportunity: {},
					template: {}
				}
			}
		}
	}
</script>