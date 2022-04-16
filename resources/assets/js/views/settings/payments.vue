<template>
	<div class="content-inner" v-if="show">
	<x-panel padding margin>
		<div slot="title">{{$t('x_settings', {title: $t('payments')})}}</div>
		<div slot="extra">
			<x-button size="sm" @click="toggleVarModal">{{$t('variables')}}</x-button>
		</div>
		<x-row line>
			<x-form-group col="8" :label="$t('default_payment_method_id')" :errors="errors.default_payment_method_id">
				<x-tag-input :value="form.default_payment_method" resource="payment_methods" column="name" name="name"
				    @input="value => { form.default_payment_method_id = value.id; form.default_payment_method = value }">
				</x-tag-input>
			</x-form-group>
			<x-form-group col="8" :label="$t('default_payment_deposit_id')" :errors="errors.default_payment_deposit_id">
				<x-tag-input :value="form.default_payment_deposit" resource="payment_deposits" column="name" name="name"
				    @input="value => { form.default_payment_deposit_id = value.id; form.default_payment_deposit = value }">
				</x-tag-input>
			</x-form-group>
			<x-form-group col="8" :label="$t('default_payment_template_id')" :errors="errors.default_payment_template_id">
				<x-tag-input :value="form.default_payment_template" resource="templates" column="name" name="name" :params="{type_id: '4'}"
				    @input="value => { form.default_payment_template_id = value.id; form.default_payment_template = value }">
				</x-tag-input>
			</x-form-group>
		</x-row>
		<x-row line>
			<x-form-group col="24" :label="$t('payment_email_subject')"
				:errors="errors.payment_email_subject"
				v-model="form.payment_email_subject">
				</x-form-group>
			<x-form-group col="24" :errors="errors.payment_email_template" :label="$t('payment_email_template')">
			  	<vue-trix v-model="form.payment_email_template"></vue-trix>
			</x-form-group>
		</x-row>
		<div slot="footer" class="flex flex-end">
			<div>
				<x-button @click="save" type="primary" :loading="isSaving">{{$t('save')}}</x-button>
			</div>
		</div>
	</x-panel>
	<settings-custom-field title="payments"></settings-custom-field>

	<mini-crud url="settings/payment_methods"
		title="payment_methods">
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
	<mini-crud url="settings/payment_deposits"
		title="payment_deposits">
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
	  <email-variables-modal v-if="showVarModal" @cancel="toggleVarModal" :items="options.email_variables"></email-variables-modal>
	</div>
</template>
<script>
	import { settings } from '@js/lib/mixins'
	import SettingsCustomField from '@js/partials/SettingsCustomField.vue'
	import MiniCrud from '@js/partials/MiniCrud.vue'
	import EmailVariablesModal from '@js/partials/EmailVariablesModal.vue'

	export default {
		mixins: [ settings ],
		components: { MiniCrud, SettingsCustomField, EmailVariablesModal },
		data() {
			return {
				redirect: 'settings/payments',
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