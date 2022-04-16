<template>
	<div class="content-inner" v-if="show">
		<x-panel padding margin>
			<div slot="title">
				{{$t(mode)}} {{$t('proposal')}}
			</div>
			<div slot="extra">
				<div>
					<a v-if="mode !== 'create'"
						:href="`/api/proposals/${form.id}/preview`" target="_blank"
						class="btn btn-default btn-sm" size="sm">{{$t('preview')}}</a>
					<x-button size="sm" @click="cancel" :disabled="isSaving">{{$t('cancel')}}</x-button>
					<x-button size="sm" @click="save" type="primary" :loading="isSaving">{{$t('save')}}</x-button>
				</div>
			</div>
			<x-row line>
				<x-form-group col="8" v-model="form.number" :label="$t('number')" disabled></x-form-group>
				<x-form-group col="8" :label="$t('contact')" :errors="errors.contact_id" optional>
					<x-typeahead-table :value="form.contact" url="contacts" name="name"
						:columns="['number', 'name']"
					  @input="value => { form.contact_id = value.id; form.contact = value }">
					</x-typeahead-table>
				</x-form-group>
				<x-form-group col="8" :label="$t('template')" :errors="errors.template_id">
					<x-tag-input :value="form.template" :params="{type_id: '1'}"
						resource="templates" column="name" name="name"
					  @input="onTemplateChange">
					</x-tag-input>
				</x-form-group>

				<x-form-group col="8" :label="$t('opportunity')" :errors="errors.opportunity_id" optional>
					<x-tag-input removable :value="form.opportunity" resource="opportunities" column="number" name="number"
						:params="{contact_id: form.contact_id}"
					    @input="value => { form.opportunity_id = value.id; form.opportunity = value }">
					</x-tag-input>
				</x-form-group>
				<x-form-group col="8" type="date" v-model="form.issue_date" :label="$t('issue_date')" :errors="errors.issue_date"></x-form-group>
				<x-form-group col="8" type="date" v-model="form.expiry_date" :label="$t('expiry_date')" :errors="errors.expiry_date"></x-form-group>
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
				redirect: 'proposals',
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