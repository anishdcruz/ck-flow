<template>
	<div class="content-inner" v-if="show">
		<x-panel padding margin>
			<div slot="title">
				<router-link to="/proposals">{{$t('proposals')}}</router-link> / {{model.number}}
			</div>
			<div slot="extra">
				<router-link :to="`/proposals`" class="btn btn-default btn-sm">
					<small class="icon icon-arrow-left-c"></small>
				</router-link>
				<x-button icon="email" size="sm" @click="toggleModal"></x-button>
				<x-dropdown size="sm" dir="right" icon="more">
					<x-dropdown-menu slot="menu">
						<x-dropdown-item v-for="(stage, i) in model.all_statuses"
							:key="stage.id"
							@click.native="markAs(stage.id)">
							{{$t('mark_as')}} {{stage.name}}
						</x-dropdown-item>

						<x-dropdown-link divide :to="`/proposals/${model.id}/edit`">
							{{$t('edit')}}
						</x-dropdown-link>
						<x-dropdown-item>
							<a @click.stop="removeDB('proposals', model.id)">{{$t('delete')}}</a>
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
				<x-group col="8" label="opportunity">
					<p v-if="model.opportunity">
						<router-link :to="`/opportunities/${model.opportunity_id}`">
							{{model.opportunity.number}}
						</router-link>
					</p>
					<p v-else>-</p>
				</x-group>
				<x-group col="8" label="issue_date">
					<p>{{model.issue_date | toDate}}</p>
				</x-group>
				<x-group col="8" label="expiry_date">
					<p>{{model.expiry_date | toDate}}</p>
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
		<x-mini :url="`contracts?proposal_id=${this.model.id}`" title="contracts">
			<div slot="extra">
		    <router-link :to="`/contracts/create?proposal_id=${model.id}`" class="btn btn-sm btn-success">
		    	{{$t('new_btn', {type: $t('contact')})}}
		    </router-link>
			</div>
			<x-tr slot="heading">
		    <x-td type="th" size="4">{{$t('number')}}</x-td>
		    <x-td type="th" size="3">{{$t('start_date')}}</x-td>
		    <x-td type="th" size="3">{{$t('expiry_date')}}</x-td>
		    <x-td type="th" size="8">{{$t('title')}}</x-td>
		    <x-td type="th" size="3">{{$t('status')}}</x-td>
		    <x-td type="th" size="4">{{$t('created_at')}}</x-td>
			</x-tr>
			<x-tr slot-scope="{ item }" @click.native="$router.push(`/contracts/${item.id}`)">
			    <x-td>{{item.number}}</x-td>
			    <x-td>{{item.start_date | toDate}}</x-td>
			    <x-td>
			    	<span v-if="item.expiry_date">{{item.expiry_date | toDate}}</span>
			    	<span v-else>-</span>
			    </x-td>
			    <x-td>{{item.title}}</x-td>
			    <x-td>
			    	<span :class="`status status-${item.status.color}`">
			    		<span class="status-text">{{item.status.name}}</span>
			    	</span>
			    </x-td>
			    <x-td>{{item.created_at | toDate}}</x-td>
			</x-tr>
		</x-mini>
		<x-mini :url="`invoices?proposal_id=${this.model.id}`" title="invoices">
			<div slot="extra">
		    <router-link :to="`/invoices/create?proposal_id=${model.id}`" class="btn btn-sm btn-success">
		    	{{$t('new_btn', {type: $t('invoice')})}}
		    </router-link>
			</div>
			<x-tr slot="heading">
		    <x-td type="th" size="4">{{$t('number')}}</x-td>
		    <x-td type="th" size="3">{{$t('issue_date')}}</x-td>
		    <x-td type="th" size="3">{{$t('due_date')}}</x-td>
		    <x-td type="th" size="8">{{$t('grand_total')}}</x-td>
		    <x-td type="th" size="3">{{$t('status')}}</x-td>
		    <x-td type="th" size="4">{{$t('created_at')}}</x-td>
			</x-tr>
			<x-tr slot-scope="{ item }" @click.native="$router.push(`/invoices/${item.id}`)">
			    <x-td>{{item.number}}</x-td>
			    <x-td>{{item.issue_date | toDate}}</x-td>
			    <x-td>
			    	<span v-if="item.due_date">{{item.due_date | toDate}}</span>
			    	<span v-else>-</span>
			    </x-td>
			    <x-td>{{item.grand_total | formatMoney}}</x-td>
			    <x-td>
			    	<span :class="`status status-${item.status.color}`">
			    		<span class="status-text">{{item.status.name}}</span>
			    	</span>
			    </x-td>
			    <x-td>{{item.created_at | toDate}}</x-td>
			</x-tr>
		</x-mini>
		<x-panel>
			<div slot="title">{{$t('preview')}}</div>
			<div slot="extra">
				<a target="_blank" :href="`/api/proposals/${model.id}/preview`" class="btn btn-default btn-sm">
					<small class="icon icon-android-download"></small>
				</a>
			</div>
			<div class="template-preview">
				<object :data="`/api/proposals/${model.id}/preview`" type="application/pdf" width="100%" height="100%">
				</object>
			</div>
		</x-panel>
		<email-modal v-if="showModal" :id="model.id" type="proposal" @cancel="toggleModal" @ok="onSaved"></email-modal>
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
					opportunity: {},
					template: {},
					status: {},
					all_statuses: [],
					custom_fields_2: []
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
			markAs(type) {
				this.$bar.start()
				this.$http.post(`/api/mark-as/proposals/${this.model.id}`, {type: type})
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