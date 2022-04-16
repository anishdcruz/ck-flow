<template>
	<div class="content-inner">
		<x-filterable :url="url" :sortable="sortable" :filter-group="filters"
			ref="filterable" :title="title">
			<div slot="extra">
				<router-link to="/opportunities/create" class="btn btn-primary">
					{{$t('new_btn', {type: $t('opportunity')})}}
				</router-link>
			</div>
			<x-tr slot="heading">
			    <x-td type="th" size="3">{{$t('number')}}</x-td>
			    <x-td type="th" size="3">{{$t('category')}}</x-td>
			    <x-td type="th" size="8">{{$t('title')}}</x-td>
			    <x-td type="th" size="4">{{$t('stage')}}</x-td>
			    <x-td type="th" size="3">{{$t('status')}}</x-td>
			    <x-td type="th" size="3">{{$t('created_at')}}</x-td>
			</x-tr>
			<x-tr slot-scope="{ item }" @click.native="$router.push(`/opportunities/${item.id}`)">
			    <x-td>{{item.number}}</x-td>
			    <x-td>{{item.category.name}}</x-td>
			    <x-td>{{item.title | trim(40)}}</x-td>
			    <x-td>
			    	<span :class="`status status-${item.stage.color}`">
			    		<span class="status-text">{{item.stage.name}}</span>
			    	</span>
			    </x-td>
			    <x-td><x-status :id="item.status_id"></x-status></x-td>
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
				url: 'opportunities',
				title: 'opportunities'
			}
		},
		computed: {
			sortable() {
				let columns = [
				  'id', 'number', 'value', 'probability', 'start_date', 'close_date', 'created_at'
				]
				return columns
			},
			filters() {
			    let groups = [{
			        title: this.$t('opportunity'),
			        filters: [
			            {name: 'id', type: 'numeric'},
			            {name: 'number', type: 'lookup', resource: 'opportunities'},
			            {name: 'title', type: 'string'},
			            {name: 'description', type: 'string'},
			            {name: 'start_date', type: 'datetime'},
			            {name: 'close_date', type: 'datetime'},
			            {name: 'value', type: 'numeric'},
			            {name: 'probability', type: 'numeric'},
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
			    }, {
			    	title: this.$t('category'),
              filters: [
                {name: 'category.name', type: 'lookup', resource: 'opportunity_categories', column: 'name'},
              ]
			    }, {
			    	title: this.$t('source'),
              filters: [
                {name: 'source.name', type: 'lookup', resource: 'opportunity_sources', column: 'name'}
              ]
			    }, {
			    	title: this.$t('status'),
              filters: [
              	{name: 'status_id', type: 'static_lookup', options: [
			            	'open', 'won', 'lost'
			            ]},
              ]
			    }, {
			    	title: this.$t('stage'),
              filters: [
                {name: 'stage.name', type: 'lookup', resource: 'opportunity_stages', column: 'name'}
              ]
			    }
			    ]

			    return groups
			}
		}
	}
</script>