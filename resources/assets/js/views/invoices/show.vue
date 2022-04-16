<template>
	<div class="content-inner" v-if="show">
		<x-panel padding margin>
			<div slot="title">
				<router-link to="/invoices">{{$t('invoices')}}</router-link> / {{model.number}}
			</div>
			<div slot="extra">
				<router-link :to="`/invoices`" class="btn btn-default btn-sm">
					<small class="icon icon-arrow-left-c"></small>
				</router-link>
				<x-button icon="email" size="sm" @click="toggleModal"></x-button>
				<x-dropdown size="sm" dir="right" icon="more">
					<x-dropdown-menu slot="menu">
						<x-dropdown-item v-for="(status, i) in model.all_statuses"
							:key="status.id"
							@click.native="markAs(status.id)">
							{{$t('mark_as')}} {{status.name}}
						</x-dropdown-item>
						<x-dropdown-item v-if="showPaymentRequest" divide>
							<a @click.stop="sentPaymentRequest">{{$t('sent_payment_request')}}</a>
						</x-dropdown-item>
						<x-dropdown-link divide :to="`/invoices/${model.id}/edit`">
							{{$t('edit')}}
						</x-dropdown-link>
						<x-dropdown-item>
							<a @click.stop="removeDB('invoices', model.id)">{{$t('delete')}}</a>
						</x-dropdown-item>
					</x-dropdown-menu>
				</x-dropdown>
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
				<x-group col="8" label="template">
					<p v-if="model.template">
						<router-link :to="`/templates/${model.template_id}`">
							{{model.template.name}}
						</router-link>
					</p>
				</x-group>
				<x-group col="8" label="proposal">
					<p v-if="model.proposal">
						<router-link :to="`/proposals/${model.proposal_id}`">
							{{model.proposal.number}}
						</router-link>
					</p>
					<p v-else>-</p>
				</x-group>
				<x-group col="8" label="contract">
					<p v-if="model.contract">
						<router-link :to="`/contracts/${model.contract_id}`">
							{{model.contract.number}}
						</router-link>
					</p>
					<p v-else>-</p>
				</x-group>
			</x-row>
			<x-row line>
				<x-group col="8" label="issue_date">
					<p>{{model.issue_date | toDate}}</p>
				</x-group>
				<x-group col="8" label="due_date">
					<p>{{model.due_date | toDate}}</p>
				</x-group>
				<x-group col="8" label="reference">
					<p v-if="model.reference">{{model.reference}}</p>
					<p v-else>-</p>
				</x-group>
			</x-row>
			<x-row line>
				<x-group col="8" label="sub_total">
					<p>{{model.sub_total | formatMoney}}</p>
				</x-group>
				<x-group col="8" label="grand_total">
					<p>{{model.grand_total | formatMoney}}</p>
				</x-group>
				<x-group col="8" label="amount_paid">
					<p>{{model.amount_paid | formatMoney}}</p>
				</x-group>
			</x-row>
			<x-row v-if="model.custom_fields_2 && model.custom_fields_2.length > 0" line>
				<custom-field-preview :field="field" :key="field.name"
					v-for="field in model.custom_fields_2" />
			</x-row>
			<x-row line>
				<x-group col="8" label="created_at">
					<pre>{{model.created_at | toDate}}</pre>
				</x-group>
				<x-group col="8" label="status">
					<span :class="`status status-${model.status.color}`">
						<span class="status-text">{{model.status.name}}</span>
					</span>
				</x-group>
			</x-row>
		</x-panel>
		<x-panel>
			<div slot="title">{{$t('preview')}}</div>
			<div slot="extra">
				<a target="_blank" :href="`/api/invoices/${model.id}/preview`" class="btn btn-default btn-sm">
					<small class="icon icon-android-download"></small>
				</a>
			</div>
			<div class="template-preview">
				<object :data="`/api/invoices/${model.id}/preview`" type="application/pdf" width="100%" height="100%">
				</object>
			</div>
		</x-panel>
		<email-modal v-if="showModal" :id="model.id" type="invoice" @cancel="toggleModal" @ok="onSaved"></email-modal>
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
					all_statuses: [],
					custom_fields: [],
					custom_field_2: []
				},
			}
		},
		computed: {
			showPaymentRequest() {
				return this.model.amount_paid != this.model.grand_total
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
			sentPaymentRequest() {
				this.$bar.start()
				this.$http.post(`/api/invoices/${this.model.id}/payment-request`)
					.then((res) => {
						if(res.data.saved) {
							this.$message.success(this.$t('payment_request_sent'))
							this.$router.push(`/payment_requests/${res.data.id}`)
						}
					})
					.finally((res) => {
						this.$bar.finish()
					})
			},
			markAs(type) {
				this.$bar.start()
				this.$http.post(`/api/mark-as/invoices/${this.model.id}`, {type: type})
					.then((res) => {
						if(res.data.saved) {
							this.$set(this.model, 'status', res.data.status)
						}
					})
					.finally((res) => {
						this.$bar.finish()
					})
			}
		}
	}
</script>