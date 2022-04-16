<template>
	<div class="content-inner" v-if="show">
		<x-panel padding margin>
			<div slot="title">{{$t('x_settings', {title: $t('organizations')})}}</div>
			<x-row line>
				<x-col span="8">
					<x-form-group :label="$t('category')" :errors="errors.default_organization_category_id">
						<x-tag-input :value="form.default_organization_category" resource="organization_categories" column="name" name="name"
						    @input="value => { form.default_organization_category_id = value.id; form.default_organization_category = value }">
						</x-tag-input>
					</x-form-group>
				</x-col>
			</x-row>
			<div slot="footer" class="flex flex-end">
				<div>
					<x-button @click="save" type="primary" :loading="isSaving">{{$t('save')}}</x-button>
				</div>
			</div>
		</x-panel>
		<settings-custom-field title="organizations"></settings-custom-field>
  	<mini-crud url="settings/organization_categories"
  		title="organization_categories">
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
	import MiniCrud from '@js/partials/MiniCrud.vue'
	import SettingsCustomField from '@js/partials/SettingsCustomField.vue'

	export default {
		mixins: [ settings ],
		components: { MiniCrud, SettingsCustomField },
		data() {
			return {
				redirect: 'settings/organizations',
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