<template>
	<div class="content-inner" v-if="show">
		<x-panel padding margin>
			<div slot="title">
				<router-link to="/contacts">{{$t('contact')}}</router-link> / {{model.name}}
				<small>({{model.number}})</small>
			</div>
			<div slot="extra">
				<router-link :to="`/contacts`" class="btn btn-default btn-sm">
					<small class="icon icon-arrow-left-c"></small>
				</router-link>
				<router-link :to="`/contacts/${model.id}/edit`" class="btn btn-default btn-sm">
					<small class="icon icon-edit"></small>
				</router-link>
				<x-button size="sm" type="error" icon="trash-b" @click="removeDB('contacts', model.id)"></x-button>
			</div>
			<x-row line>
				<x-group col="8" label="number">
					<p>{{model.number}}</p>
				</x-group>
				<x-group col="8" label="organization">
					<p v-if="model.organization">
						<router-link :to="`/organizations/${model.organization_id}`">
							{{model.organization.name}}
						</router-link>
					</p>
					<p v-else>-</p>
				</x-group>
				<x-group col="8" label="name">
					<p>{{model.name || '-'}}</p>
				</x-group>
				<x-group col="8" label="email">
					<p>{{model.email || '-'}}</p>
				</x-group>
				<x-group col="8" label="title">
					<p>{{model.title || '-'}}</p>
				</x-group>
				<x-group col="8" label="department">
					<p>{{model.department || '-'}}</p>
				</x-group>
		</x-row>
		<x-row line>
			<x-group col="8" label="phone">
				<p>{{model.phone || '-'}}</p>
			</x-group>
			<x-group col="8" label="mobile">
				<p>{{model.mobile || '-'}}</p>
			</x-group>
			<x-group col="8" label="fax">
				<p>{{model.fax || '-'}}</p>
			</x-group>
			<x-group col="8" label="website">
				<p>{{model.website || '-'}}</p>
			</x-group>
		</x-row>
		<x-row line>
			<x-group col="8" label="primary_address">
				<pre>{{model.primary_address || '-'}}</pre>
			</x-group>
			<x-group col="8" label="other_address">
				<pre>{{model.other_address || '-'}}</pre>
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
		<x-mini :url="`opportunities?contact_id=${this.model.id}`" title="opportunities">
			<div slot="extra">
		    <router-link :to="`/opportunities/create?contact_id=${model.id}`" class="btn btn-sm btn-success">
		    	{{$t('new_btn', {type: $t('opportunity')})}}
		    </router-link>
			</div>
			<x-tr slot="heading">
			    <x-td type="th" size="3">{{$t('number')}}</x-td>
			    <x-td type="th" size="3">{{$t('category')}}</x-td>
			    <x-td type="th" size="8">{{$t('title')}}</x-td>
			    <x-td type="th" size="4">{{$t('stage')}}</x-td>
			    <x-td type="th" size="3">{{$t('status')}}</x-td>
			    <x-td type="th" size="3">{{$t('created_at')}}</x-td>
			</x-tr>
			<x-tr slot-scope="{ item }" @click.native="$router.push(`/opportunities/${item.id}`)">
			    <x-td>{{item.number}}</x-td>
			    <x-td>{{item.category.name}}</x-td>
			    <x-td>{{item.title | trim(40)}}</x-td>
			    <x-td>
			    	<span :class="`status status-${item.stage.color}`">
			    		<span class="status-text">{{item.stage.name}}</span>
			    	</span>
			    </x-td>
			    <x-td><x-status :id="item.status_id"></x-status></x-td>
			    <x-td>{{item.created_at | toDate}}</x-td>
			</x-tr>
		</x-mini>
		<x-mini :url="`proposals?contact_id=${this.model.id}`" title="proposals">
			<div slot="extra">
		    <router-link :to="`/proposals/create?contact_id=${model.id}`" class="btn btn-sm btn-success">
		    	{{$t('new_btn', {type: $t('proposal')})}}
		    </router-link>
			</div>
			<x-tr slot="heading">
		    <x-td type="th" size="4">{{$t('number')}}</x-td>
		    <x-td type="th" size="4">{{$t('issue_date')}}</x-td>
		    <x-td type="th" size="4">{{$t('expiry_date')}}</x-td>
		    <x-td type="th" size="4">{{$t('opportunity')}}</x-td>
		    <x-td type="th" size="4">{{$t('status')}}</x-td>
		    <x-td type="th" size="4">{{$t('created_at')}}</x-td>
			</x-tr>
			<x-tr slot-scope="{ item }" @click.native="$router.push(`/proposals/${item.id}`)">
			    <x-td>{{item.number}}</x-td>
			    <x-td>{{item.issue_date | toDate}}</x-td>
			    <x-td>
			    	<span v-if="item.expiry_date">{{item.expiry_date | toDate}}</span>
			    	<span v-else>-</span>
			    </x-td>
			    <x-td>{{item.opportunity ? item.opportunity.number : '-'}}</x-td>
			    <x-td>
			    	<span :class="`status status-${item.status.color}`">
			    		<span class="status-text">{{item.status.name}}</span>
			    	</span>
			    </x-td>
			    <x-td>{{item.created_at | toDate}}</x-td>
			</x-tr>
		</x-mini>
		<x-mini :url="`contracts?contact_id=${this.model.id}`" title="contracts">
			<div slot="extra">
		    <router-link :to="`/contracts/create?contact_id=${model.id}`" class="btn btn-sm btn-success">
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
		<x-mini :url="`projects?contact_id=${this.model.id}`" title="projects">
			<div slot="extra">
		    <router-link :to="`/projects/create?contact_id=${model.id}`" class="btn btn-sm btn-success">
		    	{{$t('new_btn', {type: $t('project')})}}
		    </router-link>
			</div>
			<x-tr slot="heading">
		    <x-td type="th" size="4">{{$t('number')}}</x-td>
		    <x-td type="th" size="4">{{$t('start_date')}}</x-td>
		    <x-td type="th" size="4">{{$t('deadline_date')}}</x-td>
		    <x-td type="th" size="4">{{$t('progress')}}</x-td>
		    <x-td type="th" size="4">{{$t('stage')}}</x-td>
		    <x-td type="th" size="4">{{$t('created_at')}}</x-td>
			</x-tr>
			<x-tr slot-scope="{ item }" @click.native="$router.push(`/projects/${item.id}`)">
			  <x-td>{{item.number}}</x-td>
		    <x-td>{{item.start_date | toDate}}</x-td>
		    <x-td>
		    	<span v-if="item.deadline_date">{{item.deadline_date | toDate}}</span>
		    	<span v-else>-</span>
		    </x-td>
		    <x-td>{{item.progress}} %</x-td>
		    <x-td>
		    	<span :class="`status status-${item.stage.color}`">
		    		<span class="status-text">{{item.stage.name}}</span>
		    	</span>
		    </x-td>
		    <x-td>{{item.created_at | toDate}}</x-td>
			</x-tr>
		</x-mini>
		<x-mini :url="`invoices?contact_id=${this.model.id}`" title="invoices">
			<div slot="extra">
		    <router-link :to="`/invoices/create?contact_id=${model.id}`" class="btn btn-sm btn-success">
		    	{{$t('new_btn', {type: $t('invoice')})}}
		    </router-link>
			</div>
			<x-tr slot="heading">
		    <x-td type="th" size="4">{{$t('number')}}</x-td>
			    <x-td type="th" size="4">{{$t('issue_date')}}</x-td>
			    <x-td type="th" size="4">{{$t('due_date')}}</x-td>
			    <x-td type="th" size="4">{{$t('grand_total')}}</x-td>
			    <x-td type="th" size="4">{{$t('status')}}</x-td>
		    <x-td type="th" size="4">{{$t('created_at')}}</x-td>
			</x-tr>
			<x-tr slot-scope="{ item }" @click.native="$router.push(`/invoices/${item.id}`)">
			  <x-td>{{item.number}}</x-td>
			  <x-td>{{item.issue_date | toDate}}</x-td>
			  <x-td>
			  	<span v-if="item.due_date">{{item.due_date | toDate}}</span>
			  	<span v-else>-</span>
			  </x-td>
			  <x-td>{{item.grand_total}}</x-td>
			  <x-td>
			  	<span :class="`status status-${item.status.color}`">
			  		<span class="status-text">{{item.status.name}}</span>
			  	</span>
			  </x-td>
		    <x-td>{{item.created_at | toDate}}</x-td>
			</x-tr>
		</x-mini>
		<x-mini :url="`payments?contact_id=${this.model.id}`" title="payments">
			<div slot="extra">
		    <router-link :to="`/payments/create?contact_id=${model.id}`" class="btn btn-sm btn-success">
		    	{{$t('new_btn', {type: $t('payment')})}}
		    </router-link>
			</div>
			<x-tr slot="heading">
				<x-td type="th" size="3">{{$t('number')}}</x-td>
				<x-td type="th" size="4">{{$t('payment_date')}}</x-td>
				<x-td type="th" size="6">{{$t('deposit_to')}}</x-td>
				<x-td type="th" size="5">{{$t('amount_received')}}</x-td>
		    <x-td type="th" size="4">{{$t('created_at')}}</x-td>
			</x-tr>
			<x-tr slot-scope="{ item }" @click.native="$router.push(`/payments/${item.id}`)">
			  <x-td>{{item.number}}</x-td>
			  <x-td>{{item.payment_date | toDate}}</x-td>
			  <x-td>{{item.deposit.name}}</x-td>
			  <x-td>{{item.amount_received}}</x-td>
		    <x-td>{{item.created_at | toDate}}</x-td>
			</x-tr>
		</x-mini>
	</div>
</template>
<script>
	import { showable } from '@js/lib/mixins'
	import Activity from '@js/partials/Activity.vue'
	import CustomFieldPreview from '@js/partials/CustomFieldPreview.vue'
	export default {
		mixins: [ showable ],
		components: { Activity, CustomFieldPreview },
		data() {
			return {
				show: false,
				model: {
					notes: [],
					organization: {}
				}
			}
		},
		methods: {

		}
	}
</script>