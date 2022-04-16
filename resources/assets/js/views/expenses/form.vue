<template>
	<x-form padding :saving="isSaving" @save="save" @cancel="cancel" v-if="show">
		<div slot="title">{{$t(mode)}} {{$t('expense')}}</div>
		<x-row line>
			<x-form-group col="8" v-model="form.number" :label="$t('number')" disabled></x-form-group>
			<x-form-group col="8" :label="$t('vendor')" :errors="errors.vendor_id">
				<x-typeahead-table :value="form.vendor" url="vendors" name="name"
					:columns="['number', 'name']"
				  @input="value => { form.vendor_id = value.id; form.vendor = value }">
				</x-typeahead-table>
			</x-form-group>
			<x-form-group col="8" :label="$t('category')" :errors="errors.category_id">
					<x-tag-input :value="form.category" resource="expense_categories" column="name" name="name"
					    @input="value => { form.category_id = value.id; form.category = value }">
					</x-tag-input>
				</x-form-group>
		</x-row>
		<x-row line>
			<x-form-group col="8" source="textarea" v-model="form.description" :label="$t('description')" :errors="errors.description"></x-form-group>
			<x-form-group col="8" type="date" v-model="form.date" :label="$t('date')" :errors="errors.date"></x-form-group>
			<x-form-group col="8" type="number" v-model="form.amount" :label="$t('amount')" :errors="errors.amount"></x-form-group>
		</x-row>
		<x-row line>
			<x-form-group col="8" :label="$t('project')" :errors="errors.project_id" optional>
				<x-tag-input removable :value="form.project"
						resource="projects" column="number" name="number"
				    @input="value => { form.project_id = value.id; form.project = value }">
				</x-tag-input>
			</x-form-group>
			<x-form-group col="8" :label="$t('opportunity')" :errors="errors.opportunity_id" optional>
				<x-tag-input removable :value="form.opportunity"
						resource="opportunities" column="number" name="number"
				    @input="value => { form.opportunity_id = value.id; form.opportunity = value }">
				</x-tag-input>
			</x-form-group>
		</x-row>
		<div class="fields row-line" v-if="form && form.custom_fields.length > 0">
      <div :class="[`field field-${field.width}`]" v-for="(field, fIndex) in form.custom_fields">
        <field-item :editable="false" page="expenses" :field="field"></field-item>
      </div>
		</div>
	</x-form>
</template>
<script>
	import { formable } from '@js/lib/mixins'
	import FieldItem from '@js/partials/CustomFieldItem.vue'
	export default {
		mixins: [ formable ],
		components: { FieldItem },
		data() {
			return {
				redirect: 'expenses'
			}
		}
	}
</script>