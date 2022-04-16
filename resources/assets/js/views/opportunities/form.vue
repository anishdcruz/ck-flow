<template>
	<x-form padding :saving="isSaving" @save="save" @cancel="cancel" v-if="show">
		<div slot="title">{{$t(mode)}} {{$t('opportunity')}}</div>
		<x-row line>
			<x-form-group col="8" v-model="form.number" :label="$t('number')" disabled></x-form-group>
			<x-form-group col="8" :label="$t('contact')" :errors="errors.contact_id">
				<x-typeahead-table removable :value="form.contact" url="contacts" name="name"
					:columns="['number', 'name']"
				  @input="value => { form.contact_id = value.id; form.contact = value }">
				</x-typeahead-table>
			</x-form-group>
			<x-form-group col="8" v-model="form.title" :label="$t('title')" :errors="errors.title"></x-form-group>
			<x-form-group col="8" source="textarea" v-model="form.description" :label="$t('description')" :errors="errors.description"></x-form-group>
			<x-form-group col="8" v-model="form.value" :label="$t('value')" :errors="errors.value"></x-form-group>
		</x-row>
		<x-row line>
			<x-form-group col="8" type="date" v-model="form.start_date" :label="$t('start_date')" :errors="errors.start_date"></x-form-group>
			<x-form-group col="8" type="date" v-model="form.close_date" :label="$t('close_date')" :errors="errors.close_date"></x-form-group>
		</x-row>
		<x-row line>
			<x-form-group col="8" :label="$t('category')" :errors="errors.category_id" optional>
				<x-tag-input :value="form.category" resource="opportunity_categories" column="name" name="name"
				    @input="value => { form.category_id = value.id; form.category = value }">
				</x-tag-input>
			</x-form-group>
			<x-form-group col="8" :label="$t('source')" :errors="errors.source_id" optional>
				<x-tag-input :value="form.source" resource="opportunity_sources" column="name" name="name"
				    @input="value => { form.source_id = value.id; form.source = value }">
				</x-tag-input>
			</x-form-group>
			<x-form-group col="8" size="lg" source="width" v-model="form.probability" :label="$t('probability')" :errors="errors.probability"></x-form-group>
		</x-row>
		<div class="fields row-line" v-if="form && form.custom_fields.length > 0">
      <div :class="[`field field-${field.width}`]" v-for="(field, fIndex) in form.custom_fields">
        <field-item :editable="false" page="contacts" :field="field"></field-item>
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
				redirect: 'opportunities',
				form: {
					contact: {},
					category: {},
					source: {}
				}
			}
		}
	}
</script>