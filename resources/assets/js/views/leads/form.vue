<template>
	<x-form padding :saving="isSaving" @save="save" @cancel="cancel" v-if="show">
		<div slot="title">{{$t(mode)}} {{$t('lead')}}</div>
		<x-row line>
			<x-form-group col="8" v-model="form.number" :label="$t('number')" disabled></x-form-group>
			<x-form-group col="8" v-model="form.person" :label="$t('person')" :errors="errors.person"></x-form-group>
			<x-form-group col="8" v-model="form.email" :label="$t('email')" :errors="errors.email"></x-form-group>
			<x-form-group col="8" v-model="form.organization" :label="$t('organization')" :errors="errors.organization" optional></x-form-group>
			<x-form-group col="8" v-model="form.title" :label="$t('title')" :errors="errors.title" optional></x-form-group>
			<x-form-group col="8" v-model="form.department" :label="$t('department')" :errors="errors.department" optional></x-form-group>
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
				redirect: 'leads'
			}
		}
	}
</script>