<template>
	<div class="content-inner" v-if="show">
		<x-panel padding margin>
			<div slot="title">{{$t('x_settings', {title: $t('projects')})}}</div>
				<x-row line>
					<x-form-group col="8" :label="$t('project_stage_on_create_id')" :errors="errors.project_stage_on_create_id">
						<x-tag-input :value="form.project_stage_on_create" resource="project_stages" column="name" name="name"
						    @input="value => { form.project_stage_on_create_id = value.id; form.project_stage_on_create = value }">
						</x-tag-input>
					</x-form-group>
					<x-form-group col="8" :label="$t('default_project_category_id')" :errors="errors.default_project_category_id">
						<x-tag-input :value="form.default_project_category" resource="project_categories" column="name" name="name" :params="{type_id: '2'}"
						    @input="value => { form.default_project_category_id = value.id; form.default_project_category = value }">
						</x-tag-input>
					</x-form-group>
				</x-row>
			<div slot="footer" class="flex flex-end">
				<div>
					<x-button @click="save" type="primary" :loading="isSaving">{{$t('save')}}</x-button>
				</div>
			</div>
		</x-panel>
		<settings-custom-field title="projects"></settings-custom-field>
  	<mini-crud url="settings/project_categories"
  		title="project_categories">
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
		<mini-status-crud url="settings/project_stages"
			title="project_stages">
			<x-tr slot="heading">
			    <x-td type="th" size="2">{{$t('id')}}</x-td>
			    <x-td type="th" size="8">{{$t('name')}}</x-td>
			    <x-td type="th" size="4">{{$t('color')}}</x-td>
			    <x-td type="th" size="4">{{$t('locked')}}</x-td>
			    <x-td type="th" size="6" colspan="2">{{$t('created_at')}}</x-td>
			</x-tr>
			<x-tr slot-scope="{ item, edit, remove }">
			    <x-td>{{item.id}}</x-td>
			    <x-td>{{item.name}}</x-td>
			    <x-td>
			    	<span :class="`status status-${item.color}`">
			    		<span class="status-text">{{item.color}}</span>
			    	</span>
			    </x-td>
			    <x-td>{{item.locked ? $t('yes') : $t('no')}}</x-td>
			    <x-td>{{item.created_at | toDate}}</x-td>
			    <x-td>
			    	<div>
			    		<x-button @click="edit(item)" size="sm" icon="edit"></x-button>
			    		<x-button @click="remove(item)" type="error" size="sm" icon="trash-b"></x-button>
			    	</div>
			    </x-td>
			</x-tr>
		</mini-status-crud>
	  <field-type-modal v-if="showFieldModal" @cancel="toggleFieldModal"
	    @add="handleAddField"></field-type-modal>
	</div>
</template>
<script>
	import { settings } from '@js/lib/mixins'
	import MiniCrud from '@js/partials/MiniCrud.vue'
	import MiniStatusCrud from '@js/partials/MiniStatusCrud.vue'
	import SettingsCustomField from '@js/partials/SettingsCustomField.vue'

	export default {
		mixins: [ settings ],
		components: { MiniCrud, MiniStatusCrud, SettingsCustomField },
		data() {
			return {
				redirect: 'settings/projects',
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