<template>
	<x-form padding :saving="isSaving" @save="save" @cancel="cancel" v-if="show">
		<div slot="title">{{$t(mode)}} {{$t('item')}}</div>
		<x-row line>
			<x-form-group col="8" v-model="form.code" :label="$t('code')" disabled></x-form-group>
			<x-form-group col="8" :label="$t('category')" :errors="errors.category_id">
				<x-tag-input :value="form.category" resource="item_categories" column="name" name="name"
				    @input="value => { form.category_id = value.id; form.category = value }">
				</x-tag-input>
			</x-form-group>
			<x-form-group col="8" :label="$t('uom')" :errors="errors.uom_id">
				<x-tag-input :value="form.uom" resource="uoms" column="name" name="name"
				    @input="value => { form.uom_id = value.id; form.uom = value }">
				</x-tag-input>
			</x-form-group>
		</x-row>
		<x-row line>
			<x-form-group col="8" source="textarea" v-model="form.description" :label="$t('description')" :errors="errors.description"></x-form-group>
			<x-form-group col="8" v-model="form.unit_price" :label="$t('unit_price')" :errors="errors.unit_price"></x-form-group>
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
				redirect: 'items'
			}
		}
	}
</script>