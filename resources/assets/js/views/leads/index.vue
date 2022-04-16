<template>
	<div class="content-inner">
		<x-filterable :url="url" :sortable="sortable" :filter-group="filters"
			ref="filterable" :title="title">
			<div slot="extra">
				<router-link to="/leads/create" class="btn btn-primary">
					{{$t('new_btn', {type: $t('lead')})}}
				</router-link>
			</div>
			<x-tr slot="heading">
			    <x-td type="th" size="3">{{$t('number')}}</x-td>
			    <x-td type="th" size="7">{{$t('person')}}</x-td>
			    <x-td type="th" size="7">{{$t('organization')}}</x-td>
			    <x-td type="th" size="4">{{$t('status')}}</x-td>
			    <x-td type="th" size="3">{{$t('created_at')}}</x-td>
			</x-tr>
			<x-tr slot-scope="{ item }" @click.native="$router.push(`/leads/${item.id}`)">
			    <x-td>{{item.number}}</x-td>
			    <x-td>{{item.person}}</x-td>
			    <x-td>{{item.organization}}</x-td>
			    <x-td>
			    	<span :class="`status status-${item.status.color}`">
			    		<span class="status-text">{{item.status.name}}</span>
			    	</span>
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
				url: 'leads',
				title: 'leads'
			}
		},
		computed: {
			sortable() {
				let columns = [
				  'id', 'number', 'person', 'organization', 'created_at'
				]
				return columns
			},
			filters() {
			    let groups = [{
			        title: this.$t('lead'),
			        filters: [
			            {name: 'id', type: 'numeric'},
			            {name: 'number', type: 'lookup', resource: 'leads'},
			            {name: 'person', type: 'lookup', resource: 'leads'},
			            {name: 'organization', type: 'lookup', resource: 'leads'},
			            {name: 'title', type: 'string'},
			            {name: 'department', type: 'string'},
			            {name: 'email', type: 'lookup', resource: 'leads'},
			            {name: 'website', type: 'lookup', resource: 'leads'},
			            {name: 'fax', type: 'lookup', resource: 'leads'},
			            {name: 'phone', type: 'lookup', resource: 'leads'},
			            {name: 'mobile', type: 'lookup', resource: 'leads'},
			            {name: 'primary_address', type: 'string'},
			            {name: 'other_address', type: 'string'},
			            {name: 'created_at', type: 'datetime'},
			        ]
			    }, {
			    	title: this.$t('status'),
              filters: [
                {name: 'status.name', type: 'lookup', resource: 'lead_statuses', column: 'name'},
              ]
			    }]

			    return groups
			}
		}
	}
</script>