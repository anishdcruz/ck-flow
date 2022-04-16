<template>
	<div class="content-inner" v-if="show">
		<x-panel padding margin>
			<div slot="title">
				<router-link to="/projects">{{$t('projects')}}</router-link> / {{model.number}}
			</div>
			<div slot="extra">
				<router-link :to="`/projects`" class="btn btn-default btn-sm">
					<small class="icon icon-arrow-left-c"></small>
				</router-link>

				<x-dropdown size="sm" dir="right" icon="more">
					<x-dropdown-menu slot="menu">
						<x-dropdown-item v-for="(stage, i) in model.all_stages"
							:key="stage.id"
							@click.native="markAs(stage.id)">
							{{$t('mark_as')}} {{stage.name}}
						</x-dropdown-item>

						<x-dropdown-link divide :to="`/projects/${model.id}/edit`">
							{{$t('edit')}}
						</x-dropdown-link>
						<x-dropdown-item>
							<a @click.stop="removeDB('projects', model.id)">{{$t('delete')}}</a>
						</x-dropdown-item>
					</x-dropdown-menu>
				</x-dropdown>
			</div>
			<x-row line>
				<x-group col="8" label="number">
					<p>{{model.number}}</p>
				</x-group>
				<x-group col="8" label="contact">
					<p v-if="model.contact">
						<router-link :to="`/contacts/${model.contact_id}`">
							{{model.contact.name}}
						</router-link>
					</p>
				</x-group>
				<x-group col="8" label="category">
					<p v-if="model.category">
							{{model.category.name}}
					</p>
				</x-group>
				<x-group col="8" label="title">
					<p>{{model.title}}</p>
				</x-group>
				<x-group col="8" label="description">
					<pre>{{model.description}}</pre>
				</x-group>
				<x-group col="8" label="proposal">
					<p v-if="model.proposal">
						<router-link :to="`/proposals/${model.proposal_id}`">
							{{model.proposal.number}}
						</router-link>
					</p>
					<p v-else>-</p>
				</x-group>
			</x-row>
			<x-row line>
				<x-group col="8" label="start_date">
					<p>{{model.start_date | toDate}}</p>
				</x-group>
				<x-group col="8" label="estimated_finish_date">
					<p>{{model.estimated_finish_date | toDate}}</p>
				</x-group>
				<x-group col="8" label="deadline_date">
					<p v-if="model.deadline_date">{{model.deadline_date | toDate}}</p>
					<p v-else>-</p>
				</x-group>
				<x-group col="8" label="actual_start_date">
					<p v-if="model.actual_start_date">{{model.actual_start_date | toDate}}</p>
					<p v-else>-</p>
				</x-group>
				<x-group col="8" label="actual_end_date">
					<p v-if="model.actual_end_date">{{model.actual_end_date | toDate}}</p>
					<p v-else>-</p>
				</x-group>
			</x-row>
			<x-row line>
				<x-group col="8" label="estimated_cost">
					<p v-if="model.estimated_cost">{{model.estimated_cost  | formatMoney}}</p>
					<p v-else>-</p>
				</x-group>
				<x-group col="8" label="actual_cost">
					<p v-if="model.actual_cost">{{model.actual_cost  | formatMoney}}</p>
					<p v-else>-</p>
				</x-group>
				<x-group col="8" label="cost_consumption">
					<p>{{model.cost_consumption}}%</p>
				</x-group>
			</x-row>
			<x-row v-if="model.custom_fields && model.custom_fields.length > 0" line>
				<custom-field-preview :field="field" :key="field.name"
					v-for="field in model.custom_fields" />
			</x-row>
			<x-row line>
				<x-group col="8" label="created_at">
					<pre>{{model.created_at | toDate}}</pre>
				</x-group>
				<x-group col="8" label="stage">
					<span :class="`status status-${model.stage.color}`">
						<span class="status-text">{{model.stage.name}}</span>
					</span>
				</x-group>
			</x-row>
		</x-panel>
		<project-tasks :url="`project_tasks`" :project="this.model.id" title="project_tasks">
			<x-tr slot="heading">
				<x-td type="th" size="5">{{$t('title')}}</x-td>
				<x-td type="th" size="3">{{$t('start_date')}}</x-td>
				<x-td type="th" size="3">{{$t('due_date')}}</x-td>
				<x-td type="th" size="8">{{$t('description')}}</x-td>
				<x-td type="th" size="5" colspan="2">{{$t('stage')}}</x-td>
			</x-tr>
			<x-tr slot-scope="{ item, edit, remove }">
			  <x-td>{{item.title}}</x-td>
			  <x-td>{{item.start_date | toDate}}</x-td>
			  <x-td>
			  	<span v-if="item.due_date">{{item.start_date | toDate}}</span>
			  	<span v-else>-</span>
			  </x-td>
			  <x-td>{{item.description}}</x-td>
			  <x-td>
			  	<span :class="`status status-${item.stage.color}`">
		    		<span class="status-text">{{item.stage.name}}</span>
		    	</span>
			  </x-td>
			  <x-td>
			  	<div>
			  		<x-button @click="edit(item)" size="sm" icon="edit"></x-button>
			  		<x-button @click="remove(item)" type="error" size="sm" icon="trash-b"></x-button>
			  	</div>
		    </x-td>
			</x-tr>
		</project-tasks>
		<x-mini :url="`expenses?project_id=${this.model.id}`" title="expenses">
			<div slot="extra">
		    <router-link :to="`/expenses/create?project_id=${model.id}`" class="btn btn-sm btn-success">
		    	{{$t('new_btn', {type: $t('expense')})}}
		    </router-link>
			</div>
			<x-tr slot="heading">
				<x-td type="th" size="3">{{$t('number')}}</x-td>
				<x-td type="th" size="3">{{$t('date')}}</x-td>
				<x-td type="th" size="4">{{$t('category')}}</x-td>
				<x-td type="th" size="4">{{$t('amount')}}</x-td>
		    <x-td type="th" size="4">{{$t('created_at')}}</x-td>
			</x-tr>
			<x-tr slot-scope="{ item }" @click.native="$router.push(`/expenses/${item.id}`)">
			  <x-td>{{item.number}}</x-td>
			  <x-td>{{item.date | toDate}}</x-td>
			  <x-td>{{item.category.name}}</x-td>
			  <x-td>{{item.amount | formatMoney}}</x-td>
		    <x-td>{{item.created_at | toDate}}</x-td>
			</x-tr>
		</x-mini>
	</div>
</template>
<script>
	import { showable } from '@js/lib/mixins'
	import CustomFieldPreview from '@js/partials/CustomFieldPreview.vue'
	import ProjectTasks from '@js/partials/ProjectTasks.vue'

	export default {
		mixins: [ showable ],
		components: { CustomFieldPreview, ProjectTasks },
		data() {
			return {
				show: false,
				model: {
					notes: [],
					contact: {},
					proposal: {},
					catgory: {},
					stage: {},
					all_stages: []
				},
			}
		},
		methods: {

			markAs(type) {
				this.$bar.start()
				this.$http.post(`/api/mark-as/projects/${this.model.id}`, {type: type})
					.then((res) => {
						if(res.data.saved) {
							this.$set(this.model, 'stage', res.data.stage)
						}
					})
					.finally((res) => {
						this.$bar.finish()
					})
			}
		}
	}
</script>