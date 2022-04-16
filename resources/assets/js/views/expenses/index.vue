<template>
	<div class="content-inner">
		<x-filterable :url="url" :sortable="sortable" :filter-group="filters"
			ref="filterable" :title="title">
			<div slot="extra">
				<router-link to="/expenses/create" class="btn btn-primary">
					{{$t('new_btn', {type: $t('expense')})}}
				</router-link>
			</div>
			<x-tr slot="heading">
			    <x-td type="th" size="3">{{$t('number')}}</x-td>
			    <x-td type="th" size="3">{{$t('date')}}</x-td>
			    <x-td type="th" size="4">{{$t('category')}}</x-td>
			    <x-td type="th" size="7">{{$t('vendor')}}</x-td>
			    <x-td type="th" size="4">{{$t('amount')}}</x-td>
			    <x-td type="th" size="3">{{$t('created_at')}}</x-td>
			</x-tr>
			<x-tr slot-scope="{ item }" @click.native="$router.push(`/expenses/${item.id}`)">
			    <x-td>{{item.number}}</x-td>
			    <x-td>{{item.date | toDate}}</x-td>
			    <x-td>{{item.category.name}}</x-td>
			    <x-td>{{item.vendor.name}}</x-td>
			    <x-td>{{item.amount | formatMoney}}</x-td>
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
				url: 'expenses',
				title: 'expenses'
			}
		},
		computed: {
			sortable() {
				let columns = [
				  'id', 'number', 'date', 'amount', 'created_at'
				]
				return columns
			},
			filters() {
			    let groups = [{
			        title: this.$t('expense'),
			        filters: [
			            {name: 'id', type: 'numeric'},
			            {name: 'number', type: 'lookup', resource: 'contracts'},
			            {name: 'date', type: 'datetime'},
			            {name: 'description', type: 'string'},
			            {name: 'amount', type: 'numeric'},
			            {name: 'created_at', type: 'datetime'},
			        ]
			    },{
			        title: this.$t('vendor'),
			        filters: [
			            {name: 'vendor.id', type: 'numeric'},
			            {name: 'vendor.number', type: 'lookup', resource: 'vendors', column: 'number'},
			            {name: 'vendor.name', type: 'lookup', resource: 'vendors', column: 'name'},
			            {name: 'vendor.created_at', type: 'datetime'},
			        ]
			    },{
			    	title: this.$t('project'),
              filters: [
                {name: 'project.number', type: 'lookup', resource: 'projects', column: 'number'},
                {name: 'project.created_at', type: 'datetime'},
              ]
			    },{
			    	title: this.$t('opportunity'),
              filters: [
                {name: 'opportunity.number', type: 'lookup', resource: 'opportunities', column: 'number'},
                {name: 'opportunity.created_at', type: 'datetime'},
              ]
			    }]

			    return groups
			}
		}
	}
</script>