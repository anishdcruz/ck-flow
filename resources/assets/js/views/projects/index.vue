<template>
	<div class="content-inner">
		<x-filterable :url="url" :sortable="sortable" :filter-group="filters"
			ref="filterable" :title="title">
			<div slot="extra">
				<router-link to="/projects/create" class="btn btn-primary">
					{{$t('new_btn', {type: $t('project')})}}
				</router-link>
			</div>
			<x-tr slot="heading">
			    <x-td type="th" size="3">{{$t('number')}}</x-td>
			    <x-td type="th" size="3">{{$t('start_date')}}</x-td>
			    <x-td type="th" size="3">{{$t('deadline_date')}}</x-td>
			    <x-td type="th" size="6">{{$t('contact')}}</x-td>
			    <x-td type="th" size="6">{{$t('title')}}</x-td>
			    <x-td type="th" size="3">{{$t('stage')}}</x-td>
			</x-tr>
			<x-tr slot-scope="{ item }" @click.native="$router.push(`/projects/${item.id}`)">
			    <x-td>{{item.number}}</x-td>
			    <x-td>{{item.start_date | toDate}}</x-td>
			    <x-td>
			    	<span v-if="item.deadline_date">{{item.deadline_date | toDate}}</span>
			    	<span v-else>-</span>
			    </x-td>
			    <x-td>{{item.contact.name}}</x-td>
			    <x-td>{{item.title}}</x-td>
			    <x-td>
			    	<span :class="`status status-${item.stage.color}`">
			    		<span class="status-text">{{item.stage.name}}</span>
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
				url: 'projects',
				title: 'projects'
			}
		},
		computed: {
			sortable() {
				let columns = [
					'id', 'number', 'start_date', 'estimated_finish_date', 'deadline_date',
					'actual_start_date', 'actual_end_date', 'estimated_cost',
					'cost_consumption', 'actual_cost',
					'created_at'
				]
				return columns
			},
			filters() {
			    let groups = [{
			        title: this.$t('project'),
			        filters: [
			            {name: 'id', type: 'numeric'},
			            {name: 'number', type: 'lookup', resource: 'contracts'},
			            {name: 'title', type: 'lookup', resource: 'contracts'},
			            {name: 'description', type: 'string'},
			            {name: 'start_date', type: 'datetime'},
			            {name: 'estimated_finish_date', type: 'datetime'},
			            {name: 'deadline_date', type: 'datetime'},
			            {name: 'actual_start_date', type: 'datetime'},
			            {name: 'actual_end_date', type: 'datetime'},
			            {name: 'estimated_cost', type: 'numeric'},
			            {name: 'actual_cost', type: 'numeric'},
			            {name: 'cost_consumption', type: 'numeric'},
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
			    	title: this.$t('category'),
              filters: [
                {name: 'category.name', type: 'lookup', resource: 'project_categories', column: 'name'}
              ]
			    },{
			    	title: this.$t('stage'),
              filters: [
                {name: 'stage.name', type: 'lookup', resource: 'project_stages', column: 'name'}
              ]
			    }]

			    return groups
			}
		}
	}
</script>