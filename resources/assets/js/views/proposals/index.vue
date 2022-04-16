<template>
	<div class="content-inner">
		<x-filterable :url="url" :sortable="sortable" :filter-group="filters"
			ref="filterable" :title="title">
			<div slot="extra">
				<router-link to="/proposals/create" class="btn btn-primary">
					{{$t('new_btn', {type: $t('proposal')})}}
				</router-link>
			</div>
			<x-tr slot="heading">
			    <x-td type="th" size="3">{{$t('number')}}</x-td>
			    <x-td type="th" size="3">{{$t('issue_date')}}</x-td>
			    <x-td type="th" size="3">{{$t('expiry_date')}}</x-td>
			    <x-td type="th" size="9">{{$t('contact')}}</x-td>
			    <x-td type="th" size="3">{{$t('opportunity')}}</x-td>
			    <x-td type="th" size="3">{{$t('status')}}</x-td>
			</x-tr>
			<x-tr slot-scope="{ item }" @click.native="$router.push(`/proposals/${item.id}`)">
			    <x-td>{{item.number}}</x-td>
			    <x-td>{{item.issue_date | toDate}}</x-td>
			    <x-td>
			    	<span v-if="item.expiry_date">{{item.expiry_date | toDate}}</span>
			    	<span v-else>-</span>
			    </x-td>
			    <x-td>{{item.contact.name}}</x-td>
			    <x-td>{{item.opportunity ? item.opportunity.number : '-'}}</x-td>
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
				url: 'proposals',
				title: 'proposals'
			}
		},
		computed: {
			sortable() {
				let columns = [
				  'id', 'number', 'issue_date', 'expiry_date', 'created_at'
				]
				return columns
			},
			filters() {
			    let groups = [{
			        title: this.$t('proposal'),
			        filters: [
			            {name: 'id', type: 'numeric'},
			            {name: 'number', type: 'lookup', resource: 'proposals'},
			            {name: 'issue_date', type: 'datetime'},
			            {name: 'expiry_date', type: 'datetime'},
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
			    	title: this.$t('opportunity'),
              filters: [
                {name: 'opportunity.number', type: 'lookup', resource: 'opportunities', column: 'number'},
                {name: 'opportunity.created_at', type: 'datetime'},
              ]
			    },{
			    	title: this.$t('status'),
              filters: [
                {name: 'status.name', type: 'lookup', resource: 'proposal_statuses', column: 'name'}
              ]
			    }]

			    return groups
			}
		}
	}
</script>