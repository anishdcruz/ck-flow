<template>
	<div class="content-inner">
		<x-filterable :url="url" :sortable="sortable" :filter-group="filters"
			ref="filterable" :title="title">
			<div slot="extra">
				<router-link to="/organizations/create" class="btn btn-primary">
					{{$t('new_btn', {type: $t('organization')})}}
				</router-link>
			</div>
			<x-tr slot="heading">
			    <x-td type="th" size="4">{{$t('number')}}</x-td>
			    <x-td type="th" size="4">{{$t('category')}}</x-td>
			    <x-td type="th" size="13">{{$t('name')}}</x-td>
			    <x-td type="th" size="3">{{$t('created_at')}}</x-td>
			</x-tr>
			<x-tr slot-scope="{ item }" @click.native="$router.push(`/organizations/${item.id}`)">
			    <x-td>{{item.number}}</x-td>
			    <x-td>{{item.category.name}}</x-td>
			    <x-td>{{item.name}}</x-td>
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
				url: 'organizations',
				title: 'organizations'
			}
		},
		computed: {
			sortable() {
				let columns = [
				  'id', 'number', 'name',
				  'created_at'
				]
				return columns
			},
			filters() {
			    let groups = [{
			        title: this.$t('organization'),
			        filters: [
			            {name: 'id', type: 'numeric'},
			            {name: 'number', type: 'lookup', resource: 'organizations'},
			            {name: 'name', type: 'lookup', resource: 'organizations'},
			            {name: 'email', type: 'lookup', resource: 'organizations'},
			            {name: 'website', type: 'lookup', resource: 'organizations'},
			            {name: 'fax', type: 'lookup', resource: 'organizations'},
			            {name: 'phone', type: 'lookup', resource: 'organizations'},
			            {name: 'mobile', type: 'lookup', resource: 'organizations'},
			            {name: 'primary_address', type: 'string'},
			            {name: 'other_address', type: 'string'},
			            {name: 'created_at', type: 'datetime'},
			        ]
			    },{
			        title: this.$t('category'),
			        filters: [
			            {name: 'category.name', type: 'lookup', resource: 'organization_categories', column: 'name'}
			        ]
			    }]

			    return groups
			}
		}
	}
</script>