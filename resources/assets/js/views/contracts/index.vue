<template>
	<div class="content-inner">
		<x-filterable :url="url" :sortable="sortable" :filter-group="filters"
			ref="filterable" :title="title">
			<div slot="extra">
				<router-link to="/contracts/create" class="btn btn-primary">
					{{$t('new_btn', {type: $t('contract')})}}
				</router-link>
			</div>
			<x-tr slot="heading">
			    <x-td type="th" size="2">{{$t('number')}}</x-td>
			    <x-td type="th" size="3">{{$t('start_date')}}</x-td>
			    <x-td type="th" size="3">{{$t('expiry_date')}}</x-td>
			    <x-td type="th" size="5">{{$t('contact')}}</x-td>
			    <x-td type="th" size="8">{{$t('title')}}</x-td>
			    <x-td type="th" size="3">{{$t('status')}}</x-td>
			</x-tr>
			<x-tr slot-scope="{ item }" @click.native="$router.push(`/contracts/${item.id}`)">
			    <x-td>{{item.number}}</x-td>
			    <x-td>{{item.start_date | toDate}}</x-td>
			    <x-td>
			    	<span v-if="item.expiry_date">{{item.expiry_date | toDate}}</span>
			    	<span v-else>-</span>
			    </x-td>
			    <x-td>{{item.contact.name}}</x-td>
			    <x-td>{{item.title | trim}}</x-td>
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
				url: 'contracts',
				title: 'contracts'
			}
		},
		computed: {
			sortable() {
				let columns = [
				  'id', 'number', 'title', 'start_date', 'expiry_date',
				  'value', 'created_at'
				]
				return columns
			},
			filters() {
			    let groups = [{
			        title: this.$t('contract'),
			        filters: [
			            {name: 'id', type: 'numeric'},
			            {name: 'number', type: 'lookup', resource: 'contracts'},
			            {name: 'title', type: 'lookup', resource: 'contracts'},
			            {name: 'start_date', type: 'datetime'},
			            {name: 'expiry_date', type: 'datetime'},
			            {name: 'value', type: 'numeric'},
			            {name: 'auto_renewal', type: 'toggle'},
			            {name: 'no_of_months', type: 'numeric'},
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
			    	title: this.$t('type'),
              filters: [
                {name: 'type.name', type: 'lookup', resource: 'contract_types', column: 'name'}
              ]
			    },{
			    	title: this.$t('status'),
              filters: [
                {name: 'status.name', type: 'lookup', resource: 'contract_statuses', column: 'name'}
              ]
			    }]

			    return groups
			}
		}
	}
</script>