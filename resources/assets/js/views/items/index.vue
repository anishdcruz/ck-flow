<template>
	<div class="content-inner">
		<x-filterable :url="url" :sortable="sortable" :filter-group="filters"
			ref="filterable" :title="title">
			<div slot="extra">
				<router-link to="/items/create" class="btn btn-primary">
					{{$t('new_btn', {type: $t('item')})}}
				</router-link>
			</div>
			<x-tr slot="heading">
			    <x-td type="th" size="3">{{$t('code')}}</x-td>
			    <x-td type="th" size="4">{{$t('category')}}</x-td>
			    <x-td type="th" size="14">{{$t('description')}}</x-td>
			    <x-td type="th" size="3">{{$t('created_at')}}</x-td>
			</x-tr>
			<x-tr slot-scope="{ item }" @click.native="$router.push(`/items/${item.id}`)">
			    <x-td>{{item.code}}</x-td>
			    <x-td>{{item.category.name}}</x-td>
			    <x-td>{{item.description | trim(70)}}</x-td>
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
				url: 'items',
				title: 'items'
			}
		},
		computed: {
			sortable() {
				let columns = [
				  'id', 'code', 'description', 'unit_price',
    			'created_at'
				]
				return columns
			},
			filters() {
			    let groups = [{
			        title: this.$t('item'),
			        filters: [
			            {name: 'id', type: 'numeric'},
			            {name: 'code', type: 'lookup', resource: 'items'},
			            {name: 'description', type: 'string'},
			            {name: 'unit_price', type: 'numeric'},
			            {name: 'created_at', type: 'datetime'},
			        ]
			    }, {
			    	title: this.$t('category'),
              filters: [
                {name: 'category.name', type: 'lookup', resource: 'item_categories', column: 'name'},
              ]
			    }, {
			    	title: this.$t('uom'),
              filters: [
                {name: 'uom.name', type: 'lookup', resource: 'uoms', column: 'name'},
              ]
			    }]

			    return groups
			}
		}
	}
</script>