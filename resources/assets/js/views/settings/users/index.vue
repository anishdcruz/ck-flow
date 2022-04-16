<template>
	<div class="content-inner">
		<x-filterable :exportable="false" :url="url" :sortable="sortable" :filter-group="filters"
			ref="filterable" :title="title">
			<div slot="extra">
				<router-link to="/settings/users/create" class="btn btn-primary">
					{{$t('new_btn', {type: $t('user')})}}
				</router-link>
			</div>
			<x-tr slot="heading">
			    <x-td type="th" size="2">{{$t('id')}}</x-td>
			    <x-td type="th" size="6">{{$t('name')}}</x-td>
			    <x-td type="th" size="8">{{$t('email')}}</x-td>
			    <x-td type="th" size="4">{{$t('role')}}</x-td>
			    <x-td type="th" size="4">{{$t('created_at')}}</x-td>
			</x-tr>
			<x-tr slot-scope="{ item }" @click.native="$router.push(`/settings/users/${item.id}`)">
			    <x-td>{{item.id}}</x-td>
			    <x-td>{{item.name}}</x-td>
			    <x-td>{{item.email}}</x-td>
			    <x-td>{{item.role.name}}</x-td>
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
				url: 'settings/users',
				title: 'users'
			}
		},
		computed: {
			sortable() {
				let columns = [
				  'id', 'name', 'email', 'created_at'
				]
				return columns
			},
			filters() {
			    let groups = [{
			        title: this.$t('item'),
			        filters: [
			            {name: 'id', type: 'numeric'},
			            {name: 'name', type: 'lookup', resource: 'users'},
			            {name: 'email', type: 'lookup', resource: 'users'},
			            {name: 'created_at', type: 'datetime'},
			        ]
			    }, {
			    	title: this.$t('role'),
              filters: [
                {name: 'role.name', type: 'lookup', resource: 'roles', column: 'name'},
              ]
			    }]

			    return groups
			}
		}
	}
</script>