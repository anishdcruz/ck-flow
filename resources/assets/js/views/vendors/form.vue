<template>
	<x-form padding :saving="isSaving" @save="save" @cancel="cancel" v-if="show">
		<div slot="title">{{$t(mode)}} {{$t('vendor')}}</div>
		<x-row line>
			<x-form-group col="8" v-model="form.number" :label="$t('number')" disabled></x-form-group>
			<x-form-group col="8" :label="$t('category')" :errors="errors.category_id">
					<x-tag-input :value="form.category" resource="vendor_categories" column="name" name="name"
					    @input="value => { form.category_id = value.id; form.category = value }">
					</x-tag-input>
				</x-form-group>
			<x-form-group col="8" v-model="form.name" :label="$t('name')" :errors="errors.name"></x-form-group>
			<x-form-group col="8" v-model="form.email" :label="$t('email')" :errors="errors.email"></x-form-group>
		</x-row>
		<x-row line>
			<x-form-group col="8" v-model="form.phone" :label="$t('phone')" :errors="errors.phone" optional></x-form-group>
			<x-form-group col="8" v-model="form.mobile" :label="$t('mobile')" :errors="errors.mobile" optional></x-form-group>
			<x-form-group col="8" v-model="form.fax" :label="$t('fax')" :errors="errors.fax" optional></x-form-group>
			<x-form-group col="8" v-model="form.website" :label="$t('website')" :errors="errors.website" optional></x-form-group>
		</x-row>
		<x-row line>
			<x-form-group col="8" source="textarea" v-model="form.primary_address" :label="$t('primary_address')" :errors="errors.primary_address"></x-form-group>
			<x-form-group col="8" source="textarea" v-model="form.other_address" :label="$t('other_address')" :errors="errors.other_address" optional></x-form-group>
		</x-row>
		<div class="fields row-line" v-if="form && form.custom_fields.length > 0">
      <div :class="[`field field-${field.width}`]" v-for="(field, fIndex) in form.custom_fields">
        <field-item :editable="false" page="vendors" :field="field"></field-item>
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
				redirect: 'vendors'
			}
		}
	}
</script>