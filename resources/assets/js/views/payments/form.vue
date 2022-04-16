<template>
	<div class="content-inner" v-if="show">
		<x-panel padding margin>
			<div slot="title">
				{{$t(mode)}} {{$t('payment')}}
			</div>
			<x-row line>
				<x-form-group col="8" v-model="form.number" :label="$t('number')" disabled></x-form-group>
				<x-form-group col="8" :label="$t('contact')" :errors="errors.contact_id">
					<x-typeahead-table :value="form.contact" url="contacts" name="name"
						:columns="['number', 'name']" :params="{invoices: 1}"
					  @input="onContactSelect">
					</x-typeahead-table>
				</x-form-group>
				<x-form-group col="8" type="date" v-model="form.payment_date" :label="$t('payment_date')" :errors="errors.payment_date"></x-form-group>
			</x-row>
			<x-row line>
				<x-form-group col="8" :label="$t('payment_method')" :errors="errors.method_id">
					<x-tag-input :value="form.method" resource="payment_methods" column="name"
						name="name"
					  @input="value => { form.method_id = value.id; form.method = value }">
					</x-tag-input>
				</x-form-group>
				<x-form-group col="8" v-model="form.reference" :label="$t('reference')"></x-form-group>
				<x-form-group col="8" :label="$t('deposit_to')" :errors="errors.deposit_id">
					<x-tag-input :value="form.deposit"
							resource="payment_deposits" column="name" name="name"
					    @input="value => { form.deposit_id = value.id; form.deposit = value }">
					</x-tag-input>
				</x-form-group>
			</x-row>
			<div class="fields row-line" v-if="form && form.custom_fields.length > 0">
	      <div :class="[`field field-${field.width}`]" v-for="(field, fIndex) in form.custom_fields">
	        <field-item :editable="false" page="contacts" :field="field"></field-item>
	      </div>
			</div>
			<x-row line>
				<x-form-group source="textarea" col="8" v-model="form.note" :label="$t('note')" optional></x-form-group>
				<x-form-group col="8" type="number" v-model="form.amount_received" :label="$t('amount_received')"></x-form-group>
				<x-form-group col="8" type="number" v-model="form.bank_fees" :label="$t('bank_fees')"></x-form-group>

			</x-row>
		</x-panel>
		<x-panel margin>
			<div slot="title">{{$t('applied_invoices')}}</div>
			<x-table class="table-form">
				<x-thead>
					<x-tr>
					    <x-td type="th" size="4">{{$t('invoice')}}</x-td>
					    <x-td type="th" size="4">{{$t('issue_date')}}</x-td>
					    <x-td type="th" size="4">{{$t('due_date')}}</x-td>
					    <x-td type="th" size="4">{{$t('grand_total')}}</x-td>
					    <x-td type="th" size="4">{{$t('balance')}}</x-td>
					    <x-td type="th" size="4">{{$t('amount_applied')}}</x-td>
					</x-tr>
				</x-thead>
				<x-tbody v-if="form.lines.length > 0">
					<x-tr v-for="item in form.lines" :key="item.id">
					    <x-td>
					    	<span class="form-input">{{item.number}}</span>
					    </x-td>
					    <x-td>
					    	<span class="form-input">{{item.issue_date | toDate}}</span>
					    </x-td>
					    <x-td>
					    	<span class="form-input" v-if="item.due_date">{{item.due_date | toDate}}</span>
					    	<span class="form-input" v-else>-</span>
					    </x-td>
					    <x-td>
					    	<span class="form-input">{{item.grand_total | formatMoney}}</span>
					    </x-td>
					    <x-td>
					    	<span class="form-input">{{item.balance | formatMoney}}</span>
					    </x-td>
					    <x-td>
					    	<input type="number" class="form-input" v-model="item.amount_applied">
					    </x-td>
					</x-tr>
				</x-tbody>

			</x-table>
			<x-row>
				<x-col span="8" offset="16">
					<x-table class="table-form">
						<x-tfoot>
							<x-tr>
								<x-td size="12" class="table-foot">{{$t('amount_received')}}</x-td>
								<x-td size="12" align="right" class="table-foot">{{form.amount_received | formatMoney}}</x-td>
							</x-tr>
							<x-tr>
								<x-td size="12" class="table-foot">{{$t('amount_applied')}}</x-td>
								<x-td size="12" align="right" class="table-foot">{{amountApplied | formatMoney}}</x-td>
							</x-tr>
						</x-tfoot>
					</x-table>
				</x-col>
			</x-row>
	  	<div slot="footer" class="flex flex-end">
  		<div v-if="showBtn">
  			<x-button @click="cancel" :disabled="isSaving">{{$t('cancel')}}</x-button>
  			<x-button @click="save" type="primary" :loading="isSaving">{{$t('save')}}</x-button>
  		</div>
	  	</div>
		</x-panel>
	</div>
</template>
<script>
	import { formable } from '@js/lib/mixins'
	import FieldItem from '@js/partials/CustomFieldItem.vue'
	export default {
		mixins: [ formable ],
		components: { FieldItem },
		data() {
			return {
				redirect: 'payments',
				form: {
					lines: [],
					custom_fields: [],
					contact: {},
					opportunity: {},
					template: {}
				}
			}
		},
		computed: {
			amountApplied() {
				if(this.form.lines) {
					return this.form.lines.reduce((carry, item) => {
						return carry + Number(item.amount_applied)
					}, 0)
				}
				return 0
			},
			showBtn() {
				return this.amountApplied === Number(this.form.amount_received) && (this.form.amount_received > 0 && this.amountApplied > 0)
			}
		},
		methods: {
			onContactSelect(value) {
				this.form.contact_id = value.id;
				this.form.contact = value
				this.$set(this.form, 'lines', value.invoices)
			},
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