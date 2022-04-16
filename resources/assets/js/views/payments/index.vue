<template>
	<div class="content-inner">
		<x-filterable :url="url" :sortable="sortable" :filter-group="filters"
			ref="filterable" :title="title">
			<div slot="extra">
				<router-link to="/payments/create" class="btn btn-primary">
					{{$t('new_btn', {type: $t('payment')})}}
				</router-link>
			</div>
			<x-tr slot="heading">
			    <x-td type="th" size="3">{{$t('number')}}</x-td>
			    <x-td type="th" size="3">{{$t('payment_date')}}</x-td>
			    <x-td type="th" size="8">{{$t('contact')}}</x-td>
			    <x-td type="th" size="6">{{$t('deposit_to')}}</x-td>
			    <x-td type="th" size="5">{{$t('amount_received')}}</x-td>
			</x-tr>
			<x-tr slot-scope="{ item }" @click.native="$router.push(`/payments/${item.id}`)">
			    <x-td>{{item.number}}</x-td>
			    <x-td>{{item.payment_date | toDate}}</x-td>
			    <x-td>{{item.contact.name}}</x-td>
			    <x-td>{{item.deposit.name}}</x-td>
			    <x-td>{{item.amount_received | formatMoney}}</x-td>
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
				url: 'payments',
				title: 'payments'
			}
		},
		computed: {
			sortable() {
				let columns = [
				  'id', 'number', 'payment_date', 'amount_received',
				  'amount_applied', 'created_at'
				]
				return columns
			},
			filters() {
			    let groups = [{
			        title: this.$t('payment'),
			      	filters: [
			      	    {name: 'id', type: 'numeric'},
			      	    {name: 'number', type: 'lookup', resource: 'contracts'},
			      	    {name: 'reference', type: 'string'},
			      	    {name: 'payment_date', type: 'datetime'},
			      	    {name: 'amount_received', type: 'numeric'},
			      	    {name: 'amount_applied', type: 'numeric'},
			      	    {name: 'note', type: 'string'},
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
			    	title: this.$t('deposit'),
              filters: [
                {name: 'deposit.name', type: 'lookup', resource: 'payment_deposits', column: 'name'}
              ]
			    },{
			    	title: this.$t('method'),
              filters: [
                {name: 'method.name', type: 'lookup', resource: 'payment_methods', column: 'name'}
              ]
			    }]

			    return groups
			}
		}
	}
</script>