<template>
	<div class="content-inner" v-if="show">
		<x-panel padding margin>
			<div slot="title">{{$t('x_settings', {title: $t('items')})}}</div>
			<x-row line>
				<x-form-group col="8" :label="$t('category')" :errors="errors.default_item_category_id">
						<x-tag-input :value="form.default_item_category" resource="item_categories" column="name" name="name"
						    @input="value => { form.default_item_category_id = value.id; form.default_item_category = value }">
						</x-tag-input>
					</x-form-group>
				<x-form-group col="8" :label="$t('uom')" :errors="errors.default_item_uom_id">
				<x-tag-input :value="form.default_item_uom" resource="uoms" column="name" name="name"
				    @input="value => { form.default_item_uom_id = value.id; form.default_item_uom = value }">
				</x-tag-input>
			</x-form-group>
			</x-row>
			<div slot="footer" class="flex flex-end">
				<div>
					<x-button @click="save" type="primary" :loading="isSaving">{{$t('save')}}</x-button>
				</div>
			</div>
		</x-panel>
		<settings-custom-field title="items"></settings-custom-field>
  	<mini-crud url="settings/item_categories"
  		title="item_categories">
			<x-tr slot="heading">
			    <x-td type="th" size="2">{{$t('id')}}</x-td>
			    <x-td type="th" size="16">{{$t('name')}}</x-td>
			    <x-td type="th" size="6" colspan="2">{{$t('created_at')}}</x-td>
			</x-tr>
			<x-tr slot-scope="{ item, edit, remove }">
			    <x-td>{{item.id}}</x-td>
			    <x-td>{{item.name}}</x-td>
			    <x-td>{{item.created_at | toDate}}</x-td>
			    <x-td>
			    	<div>
			    		<x-button @click="edit(item)" size="sm" icon="edit"></x-button>
			    		<x-button @click="remove(item)" type="error" size="sm" icon="trash-b"></x-button>
			    	</div>
			    </x-td>
			</x-tr>
		</mini-crud>
		<mini-crud url="settings/item_uoms"
  		title="item_uoms">
			<x-tr slot="heading">
			    <x-td type="th" size="2">{{$t('id')}}</x-td>
			    <x-td type="th" size="16">{{$t('name')}}</x-td>
			    <x-td type="th" size="6" colspan="2">{{$t('created_at')}}</x-td>
			</x-tr>
			<x-tr slot-scope="{ item, edit, remove }">
			    <x-td>{{item.id}}</x-td>
			    <x-td>{{item.name}}</x-td>
			    <x-td>{{item.created_at | toDate}}</x-td>
			    <x-td>
			    	<div>
			    		<x-button @click="edit(item)" size="sm" icon="edit"></x-button>
			    		<x-button @click="remove(item)" type="error" size="sm" icon="trash-b"></x-button>
			    	</div>
			    </x-td>
			</x-tr>
		</mini-crud>
	  <field-type-modal v-if="showFieldModal" @cancel="toggleFieldModal"
	    @add="handleAddField"></field-type-modal>
	</div>
</template>
<script>
	import { settings } from '@js/lib/mixins'
	import SettingsCustomField from '@js/partials/SettingsCustomField.vue'
	import MiniCrud from '@js/partials/MiniCrud.vue'
	export default {
		mixins: [ settings ],
		components: { MiniCrud, SettingsCustomField },
		data() {
			return {
				redirect: 'settings/items',
				form: {
					fields: []
				},
				showFieldModal: false
			}
		},
		methods: {
			handleUpdateField(i, fields, e) {
			   const f = e.target.value
			   this.$set(this.form.fields, i, f)
			},
			handleRemoveField(index, fields) {
	      const r = confirm(this.$t('are_you_sure'))
        if(r != true) { return }
        fields.splice(index, 1)
      },
      handleAddField(e) {
        const f = e.target.value
        this.form.fields.push(f)
        this.toggleFieldModal();
      },
      toggleFieldModal() {
        this.showFieldModal = !this.showFieldModal
      },
		}
	}
</script>