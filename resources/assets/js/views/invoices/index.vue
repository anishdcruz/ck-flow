<template>
	<div class="content-inner">
		<x-filterable :url="url" :sortable="sortable" :filter-group="filters"
			ref="filterable" :title="title">
			<div slot="extra">
				<router-link to="/invoices/create" class="btn btn-primary">
					{{$t('new_btn', {type: $t('invoice')})}}
				</router-link>
			</div>
			<x-tr slot="heading">
			    <x-td type="th" size="3">{{$t('number')}}</x-td>
			    <x-td type="th" size="3">{{$t('issue_date')}}</x-td>
			    <x-td type="th" size="3">{{$t('due_date')}}</x-td>
			    <x-td type="th" size="8">{{$t('contact')}}</x-td>
			    <x-td type="th" size="5">{{$t('grand_total')}}</x-td>
			    <x-td type="th" size="3">{{$t('status')}}</x-td>
			</x-tr>
			<x-tr slot-scope="{ item }" @click.native="$router.push(`/invoices/${item.id}`)">
			    <x-td>{{item.number}}</x-td>
			    <x-td>{{item.issue_date | toDate}}</x-td>
			    <x-td>
			    	<span v-if="item.due_date">{{item.due_date | toDate}}</span>
			    	<span v-else>-</span>
			    </x-td>
			    <x-td>{{item.contact.name}}</x-td>
			    <x-td>{{item.grand_total | formatMoney}}</x-td>
			    <x-td>
			    	<span :class="`status status-${item.status.color}`">
			    		<span class="status-text">{{item.status.name}}</span>
			    	</span>
			    </x-td>
			</x-tr>
		</x-filterable>
	</div>
</template>
<script>
	import { indexable } from '@js/lib/mixins'
	export default {
		mixins: [indexable],
		data() {
			return {
				url: 'invoices',
				title: 'invoices'
			}
		},
		computed: {
			sortable() {
				let columns = [
					'id', 'number', 'issue_date', 'due_date',
					'sub_total', 'grand_total', 'amount_paid', 'created_at'
				]
				return columns
			},
			filters() {
			    let groups = [{
			        title: this.$t('invoice'),
			        filters: [
			            {name: 'id', type: 'numeric'},
			            {name: 'number', type: 'lookup', resource: 'invoices'},
			            {name: 'reference', type: 'string'},
			            {name: 'issue_date', type: 'datetime'},
			            {name: 'due_date', type: 'datetime'},
			            {name: 'sub_total', type: 'numeric'},
			            {name: 'grand_total', type: 'numeric'},
			            {name: 'amount_paid', type: 'numeric'},
			            {name: 'created_at', type: 'datetime'},
			        ]
			    },{
			        title: this.$t('contact'),
			        filters: [
			            {name: 'contact.id', type: 'numeric'},
			            {name: 'contact.number', type: 'lookup', resource: 'contacts', column: 'number'},
			            {name: 'contact.name', type: 'lookup', resource: 'contacts', column: 'name'},
			            {name: 'contact.created_at', type: 'datetime'},
			        ]
			    },{
			    	title: this.$t('proposal'),
              filters: [
                {name: 'proposal.number', type: 'lookup', resource: 'proposals', column: 'number'},
                {name: 'proposal.created_at', type: 'datetime'},
              ]
			    },{
			    	title: this.$t('contract'),
              filters: [
                {name: 'contract.number', type: 'lookup', resource: 'contracts', column: 'number'},
                {name: 'contract.created_at', type: 'datetime'},
              ]
			    },{
			    	title: this.$t('status'),
              filters: [
                {name: 'status.name', type: 'lookup', resource: 'invoice_statuses', column: 'name'}
              ]
			    }]

			    return groups
			}
		}
	}
</script>