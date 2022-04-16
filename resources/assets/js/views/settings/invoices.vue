<template>
	<div class="content-inner" v-if="show">
		<x-panel padding margin>
			<div slot="title">{{$t('x_settings', {title: $t('invoices')})}}</div>
			<div slot="extra">
				<x-button size="sm" @click="toggleVarModal">{{$t('variables')}}</x-button>
			</div>
			<x-row line>
				<x-form-group col="8" :label="$t('invoice_status_on_create_id')" :errors="errors.invoice_status_on_create_id">
					<x-tag-input :value="form.invoice_status_on_create" resource="invoice_statuses" column="name" name="name"
					    @input="value => { form.invoice_status_on_create_id = value.id; form.invoice_status_on_create = value }">
					</x-tag-input>
				</x-form-group>
				<x-form-group col="8" :label="$t('invoice_status_on_email_sent_id')" :errors="errors.invoice_status_on_email_sent_id">
					<x-tag-input :value="form.invoice_status_on_email_sent" resource="invoice_statuses" column="name" name="name"
					    @input="value => { form.invoice_status_on_email_sent_id = value.id; form.invoice_status_on_email_sent = value }">
					</x-tag-input>
				</x-form-group>
				<x-form-group col="8" :label="$t('invoice_status_on_payment_request_id')" :errors="errors.invoice_status_on_payment_request_id">
					<x-tag-input :value="form.invoice_status_on_payment_request" resource="invoice_statuses" column="name" name="name"
					    @input="value => { form.invoice_status_on_payment_request_id = value.id; form.invoice_status_on_payment_request = value }">
					</x-tag-input>
				</x-form-group>
				<x-form-group col="8" :label="$t('invoice_status_on_partial_payment_id')" :errors="errors.invoice_status_on_partial_payment_id">
					<x-tag-input :value="form.invoice_status_on_partial_payment" resource="invoice_statuses" column="name" name="name"
					    @input="value => { form.invoice_status_on_partial_payment_id = value.id; form.invoice_status_on_partial_payment = value }">
					</x-tag-input>
				</x-form-group>
				<x-form-group col="8" :label="$t('invoice_status_on_complete_payment_id')" :errors="errors.invoice_status_on_complete_payment_id">
					<x-tag-input :value="form.invoice_status_on_complete_payment" resource="invoice_statuses" column="name" name="name"
					    @input="value => { form.invoice_status_on_complete_payment_id = value.id; form.invoice_status_on_complete_payment = value }">
					</x-tag-input>
				</x-form-group>
				<x-form-group col="8" :label="$t('default_invoice_template_id')" :errors="errors.default_invoice_template_id">
					<x-tag-input :value="form.default_invoice_template" resource="templates" column="name" name="name" :params="{type_id: '3'}"
					    @input="value => { form.default_invoice_template_id = value.id; form.default_invoice_template = value }">
					</x-tag-input>
				</x-form-group>
				<x-form-group col="8" type="number" :label="$t('due_date_after_days')" :errors="errors.due_date_after_days" v-model="form.due_date_after_days"></x-form-group>
				<x-form-group col="16" :label="$t('receive_payment_on_status_ids')" :errors="errors.receive_payment_on_status_ids">
					<x-tag-input multiple :value="form.receive_payment_on_status_ids" resource="invoice_statuses" column="name" name="name"
					    @input="value => { form.receive_payment_on_status_ids = value; }">
					</x-tag-input>
				</x-form-group>
			</x-row>
			<x-row line>
				<x-form-group col="24" :label="$t('invoice_email_subject')"
					:errors="errors.invoice_email_subject"
					v-model="form.invoice_email_subject">
					</x-form-group>
				<x-form-group col="24" :errors="errors.invoice_email_template" :label="$t('invoice_email_template')">
				  	<vue-trix v-model="form.invoice_email_template"></vue-trix>
				</x-form-group>
			</x-row>
			<div slot="footer" class="flex flex-end">
				<div>
					<x-button @click="save" type="primary" :loading="isSaving">{{$t('save')}}</x-button>
				</div>
			</div>
		</x-panel>
		<settings-custom-field title="invoices"></settings-custom-field>
		<mini-status-crud url="settings/invoice_statuses"
			title="invoice_statuses">
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
	  <email-variables-modal v-if="showVarModal" @cancel="toggleVarModal" :items="options.email_variables"></email-variables-modal>
	</div>
</template>
<script>
	import { settings } from '@js/lib/mixins'
	import MiniCrud from '@js/partials/MiniCrud.vue'
	import MiniStatusCrud from '@js/partials/MiniStatusCrud.vue'
	import SettingsCustomField from '@js/partials/SettingsCustomField.vue'
	import EmailVariablesModal from '@js/partials/EmailVariablesModal.vue'

	export default {
		mixins: [ settings ],
		components: { MiniCrud, MiniStatusCrud, SettingsCustomField, EmailVariablesModal },
		data() {
			return {
				redirect: 'settings/invoices',
				form: {
					fields: []
				},
				options: {},
				showFieldModal: false,
				showVarModal: false
			}
		},
		methods: {
			setData(res) {
				this.$set(this.$data, 'form', res.data.form)
				this.$set(this.$data, 'options', res.data.options)
				this.$bar.finish()
				this.show = true
			},
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
      toggleVarModal() {
      	this.showVarModal = !this.showVarModal
      }
		}
	}
</script>