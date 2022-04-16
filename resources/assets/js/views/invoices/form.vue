<template>
	<div class="content-inner" v-if="show">
		<x-panel padding margin>
			<div slot="title">
				{{$t(mode)}} {{$t('invoice')}}
			</div>
			<x-row line>
				<x-form-group col="8" v-model="form.number" :label="$t('number')" disabled></x-form-group>
				<x-form-group col="8" :label="$t('contact')" :errors="errors.contact_id">
					<x-typeahead-table :value="form.contact" url="contacts" name="name"
						:columns="['number', 'name']"
					  @input="value => { form.contact_id = value.id; form.contact = value }">
					</x-typeahead-table>
				</x-form-group>
				<x-form-group col="8" :label="$t('template')" :errors="errors.template_id">
					<x-tag-input :value="form.template" :params="{type_id: '3'}"
						resource="templates" column="name" name="name"
					  @input="onTemplateChange">
					</x-tag-input>
				</x-form-group>
				<x-form-group col="8" :label="$t('proposal')" :errors="errors.proposal_id" optional>
					<x-tag-input removable :value="form.proposal" :params="{contact_id: form.contact_id}"
							resource="proposals" column="number" name="number"
					    @input="value => { form.proposal_id = value.id; form.proposal = value }">
					</x-tag-input>
				</x-form-group>
				<x-form-group col="8" :label="$t('contract')" :errors="errors.contract_id" optional>
					<x-tag-input removable :value="form.contract" :params="{contact_id: form.contact_id}"
							resource="contracts" column="number" name="number"
					    @input="value => { form.contract_id = value.id; form.contract = value }">
					</x-tag-input>
				</x-form-group>
				<x-form-group col="8" v-model="form.reference" :label="$t('reference')" optional></x-form-group>
			</x-row>
			<x-row line>
				<x-form-group col="8" type="date" v-model="form.issue_date" :label="$t('issue_date')" :errors="errors.issue_date"></x-form-group>
				<x-form-group col="8" type="date" v-model="form.due_date" :label="$t('due_date')" :errors="errors.due_date"></x-form-group>
			</x-row>
			<div class="fields row-line" v-if="form && form.custom_fields_2.length > 0">
	      <div :class="[`field field-${field.width}`]" v-for="(field, fIndex) in form.custom_fields_2">
	        <custom-field-item :editable="false" page="contacts" :field="field"></custom-field-item>
	      </div>
			</div>
			<template v-for="page in form.custom_fields">
				<x-row v-if="showSection(page)" line>
		  		<x-col span="24">
		  			<x-row>
		  				<x-col span="12">
		  					<div class="template-instruction">
		  						<h3>{{page.title}}</h3>
		  						<small>{{page.instruction}}</small>
		  					</div>
		  				</x-col>
		  			</x-row>
			  		<div v-for="(section, index) in page.user_fields">
	  					<div class="fields" v-if="section && section.fields.length > 0">
	  			      <div :class="[`field field-${field.width}`]" v-for="(field, fIndex) in section.fields">
	  			        <field-item :editable="false" page="customer" :field="field"></field-item>
	  			      </div>
	  					</div>
			  		</div>
		  		</x-col>
		  	</x-row>
	  	</template>
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
	import FieldItem from '@js/partials/FieldItem.vue'
	import CustomFieldItem from '@js/partials/CustomFieldItem.vue'
	export default {
		mixins: [ formable ],
		components: { FieldItem, CustomFieldItem },
		data() {
			return {
				redirect: 'invoices',
				form: {
					custom_fields: [],
					custom_fields_2: [],
					contact: {},
					opportunity: {},
					template: {}
				}
			}
		},
		computed: {
			showSection() {
				return (page) => {
					let count = 0
					page.user_fields.forEach((section) => {
						if(section.fields.length) {
							count++
						}
					})
					return count > 0
				}
			}
		},
		methods: {
			onTemplateChange(value) {
				this.form.template_id = value.id
				this.$set(this.form, 'custom_fields', value.custom_fields)
				this.form.template = {
					id: value.id,
					name: value.name
				}
			}
		}
	}
</script>