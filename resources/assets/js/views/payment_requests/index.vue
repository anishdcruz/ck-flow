<template>
	<div class="content-inner">
		<x-filterable :url="url" :sortable="sortable" :filter-group="filters"
			ref="filterable" :title="title">
			<x-tr slot="heading">
			    <x-td type="th" size="3">{{$t('number')}}</x-td>
			    <x-td type="th" size="4">{{$t('invoice')}}</x-td>
			    <x-td type="th" size="8">{{$t('email')}}</x-td>
			    <x-td type="th" size="3">{{$t('expiry_at')}}</x-td>
			    <x-td type="th" size="3">{{$t('received_at')}}</x-td>
			    <x-td type="th" size="3">{{$t('created_at')}}</x-td>
			</x-tr>
			<x-tr slot-scope="{ item }" @click.native="$router.push(`/payment_requests/${item.id}`)">
			    <x-td>{{item.number}}</x-td>
			    <x-td>{{item.invoice.number}}</x-td>
			    <x-td>{{item.email}}</x-td>
			    <x-td>{{item.expiry_at | toDate}}</x-td>
			    <x-td>
			    	<span v-if="item.payment_received_at">{{item.payment_received_at | toDate}}</span>
			    	<span v-else>-</span>
			    </x-td>
			    <x-td>{{item.created_at | toDate}}</x-td>
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
				url: 'payment_requests',
				title: 'payment_requests'
			}
		},
		computed: {
			sortable() {
				let columns = [
				  'id', 'number', 'email', 'expiry_at',
    			'payment_received_at', 'created_at'
				]
				return columns
			},
			filters() {
			    let groups = [{
			        title: this.$t('payment_request'),
			        filters: [
			            {name: 'id', type: 'numeric'},
			            {name: 'number', type: 'lookup', resource: 'payment_requests'},
			            {name: 'email', type: 'string'},
			            {name: 'uuid', type: 'string'},
			            {name: 'payment_received_at', type: 'datetime'},
			            {name: 'expiry_at', type: 'datetime'},
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
			    	title: this.$t('invoice'),
              filters: [
                {name: 'invoice.number', type: 'lookup', resource: 'invoices', column: 'number'},
                {name: 'invoice.created_at', type: 'datetime'},
              ]
			    }]

			    return groups
			}
		}
	}
</script>