<template>
	<div class="content-inner">
		<x-filterable :url="url" :sortable="sortable" :filter-group="filters"
			ref="filterable" :title="title">
			<div slot="extra">
				<router-link to="/vendors/create" class="btn btn-primary">
					{{$t('new_btn', {type: $t('vendor')})}}
				</router-link>
			</div>
			<x-tr slot="heading">
			    <x-td type="th" size="3">{{$t('number')}}</x-td>
			    <x-td type="th" size="10">{{$t('name')}}</x-td>
			    <x-td type="th" size="8">{{$t('category')}}</x-td>
			    <x-td type="th" size="3">{{$t('created_at')}}</x-td>
			</x-tr>
			<x-tr slot-scope="{ item }" @click.native="$router.push(`/vendors/${item.id}`)">
			    <x-td>{{item.number}}</x-td>
			    <x-td>{{item.name}}</x-td>
			    <x-td>{{item.category.name}}</x-td>
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
				url: 'vendors',
				title: 'vendors'
			}
		},
		computed: {
			sortable() {
				let columns = [
				  'id', 'number', 'name', 'total_expense',
    	'created_at'
				]
				return columns
			},
			filters() {
			    let groups = [{
			        title: this.$t('vendor'),
			        filters: [
			            {name: 'id', type: 'numeric'},
			            {name: 'number', type: 'lookup', resource: 'contacts'},
			            {name: 'name', type: 'lookup', resource: 'contacts'},
			            {name: 'email', type: 'lookup', resource: 'contacts'},
			            {name: 'website', type: 'lookup', resource: 'contacts'},
			            {name: 'fax', type: 'lookup', resource: 'contacts'},
			            {name: 'phone', type: 'lookup', resource: 'contacts'},
			            {name: 'mobile', type: 'lookup', resource: 'contacts'},
			            {name: 'primary_address', type: 'string'},
			            {name: 'other_address', type: 'string'},
			            {name: 'total_expense', type: 'numeric'},
			            {name: 'created_at', type: 'datetime'},
			        ]
			    }, {
			    	title: this.$t('category'),
              filters: [
                {name: 'category.name', type: 'lookup', resource: 'vendor_categories', column: 'name'},
              ]
			    }]

			    return groups
			}
		}
	}
</script>