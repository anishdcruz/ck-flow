<template>
	<div class="content-inner" v-if="show">
		<x-panel padding margin>
			<div slot="title">
				<router-link to="/opportunities">{{$t('opportunity')}}</router-link> / {{model.title}}
				<small>({{model.number}})</small>
			</div>
			<div slot="extra">
				<router-link :to="`/opportunities`" class="btn btn-default btn-sm">
					<small class="icon icon-arrow-left-c"></small>
				</router-link>

				<x-dropdown size="sm" dir="right" icon="more">
					<x-dropdown-menu slot="menu">
						<x-dropdown-item @click.native="markStatusAs('won')">{{$t('mark_as_won')}}</x-dropdown-item>
						<x-dropdown-item @click.native="markStatusAs('lost')">{{$t('mark_as_lost')}}</x-dropdown-item>
						<x-dropdown-item :divide="i === 0" v-for="(stage, i) in model.all_stages"
							:key="stage.id"
							@click.native="markAs(stage.id)">
							{{$t('mark_as')}} {{stage.name}}
						</x-dropdown-item>

						<x-dropdown-link divide :to="`/opportunities/${model.id}/edit`">
							{{$t('edit')}}
						</x-dropdown-link>
						<x-dropdown-item>
							<a @click.stop="removeDB('opportunities', model.id)">{{$t('delete')}}</a>
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
					<p v-else>-</p>
				</x-group>
				<x-group col="8" label="title">
					<p>{{model.title || '-'}}</p>
				</x-group>
				<x-group col="8" label="description">
					<pre>{{model.description || '-'}}</pre>
				</x-group>
				<x-group col="8" label="value">
					<p>{{model.value | formatMoney}}</p>
				</x-group>
			</x-row>
			<x-row line>
				<x-group col="8" label="start_date">
					<p>{{model.start_date | toDate}}</p>
				</x-group>
				<x-group col="8" label="close_date">
					<p>{{model.close_date | toDate}}</p>
				</x-group>
			</x-row>
			<x-row line>
				<x-group col="8" label="category">
					<p v-if="model.category">
						{{model.category.name}}
					</p>
					<p v-else>-</p>
				</x-group>
				<x-group col="8" label="source">
					<p v-if="model.source">
						{{model.source.name}}
					</p>
					<p v-else>-</p>
				</x-group>
				<x-group col="8" label="probability">
					<pre>{{model.probability}} %</pre>
				</x-group>
			</x-row>
			<x-row v-if="model.custom_fields && model.custom_fields.length > 0" line>
				<custom-field-preview :field="field" :key="field.name"
					v-for="field in model.custom_fields" />
			</x-row>
			<x-row line>
				<x-group col="8" label="created_at">
					<p>{{model.created_at | toDate}}</p>
				</x-group>
				<x-group col="8" label="status">
					<p><x-status :id="model.status_id"></x-status></p>
				</x-group>
				<x-group col="8" label="stage">
					<span :class="`status status-${model.stage.color}`">
						<span class="status-text">{{model.stage.name}}</span>
					</span>
				</x-group>
			</x-row>
		</x-panel>
		<x-mini :url="`proposals?opportunity_id=${this.model.id}`" title="proposals">
			<div slot="extra">
		    <router-link :to="`/proposals/create?opportunity_id=${model.id}`" class="btn btn-sm btn-success">
		    	{{$t('new_btn', {type: $t('proposal')})}}
		    </router-link>
			</div>
			<x-tr slot="heading">
				<x-td type="th" size="3">{{$t('number')}}</x-td>
		    <x-td type="th" size="3">{{$t('issue_date')}}</x-td>
		    <x-td type="th" size="3">{{$t('expiry_date')}}</x-td>
		    <x-td type="th" size="9">{{$t('contact')}}</x-td>
		    <x-td type="th" size="3">{{$t('status')}}</x-td>
		    <x-td type="th" size="3">{{$t('created_at')}}</x-td>
			</x-tr>
			<x-tr slot-scope="{ item }" @click.native="$router.push(`/proposals/${item.id}`)">
			  <x-td>{{item.number}}</x-td>
		    <x-td>{{item.issue_date | toDate}}</x-td>
		    <x-td>
		    	<span v-if="item.expiry_date">{{item.expiry_date | toDate}}</span>
		    	<span v-else>-</span>
		    </x-td>
		    <x-td>{{item.contact.name}}</x-td>
		    <x-td>
		    	<span :class="`status status-${item.status.color}`">
		    		<span class="status-text">{{item.status.name}}</span>
		    	</span>
		    </x-td>
		    <x-td>{{item.created_at | toDate}}</x-td>
			</x-tr>
		</x-mini>

		<x-mini :url="`expenses?opportunity_id=${this.model.id}`" title="expenses">
			<div slot="extra">
		    <router-link :to="`/expenses/create?opportunity_id=${model.id}`" class="btn btn-sm btn-success">
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
		</x-mini>
	</div>
</template>
<script>
	import { showable } from '@js/lib/mixins'
	import CustomFieldPreview from '@js/partials/CustomFieldPreview.vue'
	export default {
		mixins: [ showable ],
		components: { CustomFieldPreview },
		data() {
			return {
				show: false,
				model: {
					notes: [],
					contact: {},
					category: {},
					source: {},
					stage: {},
					all_stages: []
				},
			}
		},
		methods: {
			markStatusAs(type) {
				this.$bar.start()
				this.$http.post(`/api/mark-as/opportunities/${this.model.id}/status`, {type: type})
					.then((res) => {
						if(res.data.saved) {
							this.$set(this.model, 'status_id', res.data.status_id)
							this.$set(this.model, 'stage', res.data.stage)
						}
					})
					.finally((res) => {
						this.$bar.finish()
					})
			},
			markAs(type) {
				this.$bar.start()
				this.$http.post(`/api/mark-as/opportunities/${this.model.id}`, {type: type})
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