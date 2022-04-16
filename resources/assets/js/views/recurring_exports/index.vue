<template>
	<div class="content-inner">
		<x-filterable :exportable="false" :url="url" :sortable="sortable" :filter-group="filters"
			ref="filterable" :title="title">
			<x-tr slot="heading">
			    <x-td type="th" size="6">{{$t('name')}}</x-td>
			    <x-td type="th" size="5">{{$t('frequency')}}</x-td>
			    <x-td type="th" size="5">{{$t('model')}}</x-td>
			    <x-td type="th" size="5">{{$t('email')}}</x-td>
			    <x-td type="th" size="3">{{$t('created_at')}}</x-td>
			</x-tr>
			<x-tr slot-scope="{ item }" @click.native="$router.push(`/recurring_exports/${item.id}`)">
			    <x-td>{{item.name}}</x-td>
			    <x-td>{{$t(item.frequency)}}</x-td>
			    <x-td>{{$t(item.model)}}</x-td>
			    <x-td>{{item.email_to}}</x-td>
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
				url: 'recurring_exports',
				title: 'recurring_exports'
			}
		},
		computed: {
			sortable() {
				let columns = [
				  'id', 'created_at'
				]
				return columns
			},
			filters() {
			    let groups = [{
			        title: this.$t('item'),
			        filters: [
			            {name: 'id', type: 'numeric'},
			            {name: 'name', type: 'string'},
			            {name: 'email_to', type: 'string'},
			            {name: 'frequency', type: 'string'},
			            {name: 'created_at', type: 'datetime'},
			        ]
			    }]

			    return groups
			}
		}
	}
</script>