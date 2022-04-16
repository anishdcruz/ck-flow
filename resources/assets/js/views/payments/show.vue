<template>
	<div class="content-inner" v-if="show">
		<x-panel padding margin>
			<div slot="title">
				<router-link to="/payments">{{$t('payments')}}</router-link> / {{model.number}}
			</div>
			<div slot="extra">
				<router-link :to="`/payments`" class="btn btn-default btn-sm">
					<small class="icon icon-arrow-left-c"></small>
				</router-link>
				<x-button icon="email" size="sm" @click="toggleModal"></x-button>
				<x-button size="sm" type="error" icon="trash-b" @click="removeDB('payments', model.id)"></x-button>
			</div>
			<x-row line>
				<x-group col="8" label="number">
					<p>{{model.number}}</p>
				</x-group>
				<x-group col="8" label="contact">
					<p v-if="model.contact">
						<router-link :to="`/contacts/${model.contact_id}`">
							{{model.contact.name}}
						</router-link>
					</p>
				</x-group>
				<x-group col="8" label="payment_date">
					<p>{{model.payment_date | toDate}}</p>
				</x-group>
			</x-row>
			<x-row line>
				<x-group col="8" label="payment_method">
					<p>{{model.method.name}}</p>
				</x-group>
				<x-group col="8" label="reference">
					<p>{{model.reference}}</p>
				</x-group>
				<x-group col="8" label="deposit_to">
					<p>{{model.deposit.name}}</p>
				</x-group>
			</x-row>
			<x-row line>
				<x-group col="8" label="amount_received">
					<p>{{model.amount_received | formatMoney}}</p>
				</x-group>
				<x-group col="8" label="amount_applied">
					<p>{{model.amount_applied | formatMoney}}</p>
				</x-group>
				<x-group col="8" label="bank_fees">
					<p>{{model.bank_fees | formatMoney}}</p>
				</x-group>
				<x-group col="8" label="net_amount">
					<p>{{model.net_amount | formatMoney}}</p>
				</x-group>
			</x-row>
			<x-row v-if="model.custom_fields && model.custom_fields.length > 0" line>
				<custom-field-preview :field="field" :key="field.name"
					v-for="field in model.custom_fields" />
			</x-row>
			<x-row line>
				<x-group col="8" label="created_at">
					<pre>{{model.created_at | toDate}}</pre>
				</x-group>
			</x-row>
		</x-panel>
		<x-panel margin>
			<div slot="title">{{$t('applied_invoices')}}</div>
			<x-table class="table table-link">
				<x-thead>
					<x-tr>
					    <x-td type="th" size="5">{{$t('invoice')}}</x-td>
					    <x-td type="th" size="5">{{$t('issue_date')}}</x-td>
					    <x-td type="th" size="5">{{$t('due_date')}}</x-td>
					    <x-td type="th" size="5">{{$t('grand_total')}}</x-td>
					    <x-td type="th" size="4">{{$t('amount_applied')}}</x-td>
					</x-tr>
				</x-thead>
				<x-tbody>
					<x-tr v-for="item in model.lines" :key="item.id"
						@click.native="$router.push(`/invoices/${item.invoice_id}`)">
					    <x-td>
					    	{{item.invoice.number}}
					    </x-td>
					    <x-td>{{item.invoice.issue_date | toDate}}</x-td>
					    <x-td>
					    	<span v-if="item.invoice.due_date">{{item.invoice.due_date | toDate}}</span>
					    	<span v-else>-</span>
					    </x-td>
					    <x-td>{{item.invoice.grand_total | formatMoney}}</x-td>
					    <x-td>{{item.amount_applied | formatMoney}}</x-td>
					</x-tr>
				</x-tbody>
			</x-table>
		</x-panel>
		<x-panel>
			<div slot="title">{{$t('preview')}}</div>
			<div slot="extra">
				<a target="_blank" :href="`/api/payments/${model.id}/preview`" class="btn btn-default btn-sm">
					<small class="icon icon-android-download"></small>
				</a>
			</div>
			<div class="template-preview">
				<object :data="`/api/payments/${model.id}/preview`" type="application/pdf" width="100%" height="100%">
				</object>
			</div>
		</x-panel>
		<email-modal v-if="showModal" :id="model.id" type="payment" @cancel="toggleModal" @ok="onSaved"></email-modal>
	</div>
</template>
<script>
	import { showable } from '@js/lib/mixins'
	import CustomFieldPreview from '@js/partials/CustomFieldPreview.vue'
	import EmailModal from '@js/partials/EmailModal.vue'
	export default {
		mixins: [ showable ],
		components: { CustomFieldPreview, EmailModal },
		data() {
			return {
				showModal: false,
				show: false,
				model: {
					notes: [],
					contact: {},
					proposal: {},
					catgory: {},
					status: {},
					lines: []
				}
			}
		},
		methods: {
			onSaved() {
				this.showModal = false
				const id = Math.random().toString(36).substring(7)
				this.$router.push(`?id=${id}`)
			},
			toggleModal() {
				this.showModal = !this.showModal
			},
		}
	}
</script>