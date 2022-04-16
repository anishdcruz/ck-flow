<template>
	<x-modal  width="950" @cancel="$emit('cancel')">
		<div slot="title">
			<div>
				<span>{{$t('new_card')}}</span>
			</div>
		</div>
		<div>
			<field-tab :tabs="tabs">
				<div slot="basic">
					<div>
						<x-row>
							<x-form-group col="6" :label="$t('field_type')">
								<select class="form-input" v-model="form.resource">
									<option :value="resource"
										v-for="resource in resources">{{$t(resource)}}</option>
								</select>
							</x-form-group>
							<x-form-group col="6" :label="$t('field_type')">
								<select class="form-input" v-model="form.metric_type">
									<option :value="metric_type"
										v-for="metric_type in metrics">{{$t(metric_type)}}</option>
								</select>
							</x-form-group>
							<x-col span="12">
								<div class="form-group">
									<label class="form-label">
									  {{$t('card_label')}}
									</label>
									<x-input :value="form.label" v-model="form.card_label"></x-input>
								</div>
							</x-col>
						</x-row>
						<x-row>
							<x-form-group col="6" :label="$t('time_peroid')" v-if="form.metric_type ==='chart'">
								<select class="form-input" v-model="form.time_peroid">
									<option :value="time_peroid"
										v-for="time_peroid in time_peroids">{{$t(time_peroid)}}</option>
								</select>
							</x-form-group>
							<x-form-group col="6" :label="$t('chart_type')" v-if="form.metric_type ==='chart'">
								<select class="form-input" v-model="form.chart_type">
									<option :value="chart_type"
										v-for="chart_type in chart_types">{{$t(chart_type)}}</option>
								</select>
							</x-form-group>
							<x-form-group col="6" :label="$t('group_by')" v-if="form.metric_type ==='chart'">
								<select class="form-input" v-model="form.group_by">
									<option :value="date"
										v-for="date in availableDates">{{$t(date)}}</option>
								</select>
							</x-form-group>
							<x-form-group col="6" :label="$t('color')" v-if="form.metric_type ==='chart'">
								<select class="form-input" v-model="form.color">
									<option :value="color"
										v-for="color in colors">{{color}}</option>
								</select>
							</x-form-group>
						</x-row>
					</div>
				</div>
				<div slot="filters">
					<div class="metrics-filter-title">
						<div class="filters-title">
						    <span>{{$t(title)}} {{$t('match')}}</span>
						    <select v-model="query.filter_match" class="form-input form-input-sm">
						        <option value="and">{{$t('all')}}</option>
						        <option value="or">{{$t('any')}}</option>
						    </select>
						    <span>{{$t('of_the_following')}}</span>
						</div>
					</div>
					<div class="metrics-filter">
					  <div class="filters">
					    <div class="filters-item" v-for="(f, i) in filterCandidates">
					      <div class="filters-column">
					        <select class="form-input" @input="onColumnSelect(f, i, $event)">
					          <option value="">{{$t('select_a_filter')}}</option>
					          <optgroup v-for="group in filterGroups" :label="group.title">
					            <option v-for="x in group.filters" :value="JSON.stringify(x)"
					              :selected="f.column && x.name === f.column.name">
					              {{x.title}}
					            </option>
					          </optgroup>
					        </select>
					        <small class="error-control" v-if="errors[`f.${i}.column`]">
					          {{errors[`f.${i}.column`][0]}}
					        </small>
					      </div>
					      <div class="filters-operator" v-if="f.column">
					        <select class="form-input" @input="onOperatorSelect(f, i, $event)">
					          <option v-for="y in fetchOperators(f)" :value="JSON.stringify(y)"
					            :selected="f.operator && y.name === f.operator.name">
					            {{$t(y.name)}}
					          </option>
					        </select>
					        <small class="error-control" v-if="errors[`f.${i}.operator`]">
					          {{errors[`f.${i}.operator`][0]}}
					        </small>
					      </div>
					      <template v-if="f.column && f.operator">
					          <div class="filters-full" v-if="f.operator.component === 'single'">
					              <input type="text" class="form-input" :placeholder="f.placeholder"
					                  v-model="f.query_1">
					              <small class="error-control" v-if="errors[`f.${i}.query_1`]">
					                      {{errors[`f.${i}.query_1`][0]}}
					              </small>
					          </div>

					          <div class="filters-full" v-if="f.operator.component === 'lookup'">
					            <x-tag-input :value="f.query_1"
					                :resource="f.column.resource"
					                :column="f.column.column || f.column.name"
					                :name="f.column.column || f.column.name"
					                @input="value => { f.query_1 = value }" multiple>
					            </x-tag-input>
					            <small class="error-control" v-if="errors[`f.${i}.query_1`]">
					              {{errors[`f.${i}.query_1`][0]}}
					            </small>
					          </div>

					          <div class="filters-full" v-if="f.operator.component === 'static_lookup'">
					              <x-select :value="f.query_1" @input="value => { f.query_1 = value }" :options="setOptions(f.column.options)" multiple></x-select>
					              <small class="error-control" v-if="errors[`f.${i}.query_1`]">
					                      {{errors[`f.${i}.query_1`][0]}}
					              </small>
					          </div>
					          <template v-else-if="f.operator.component === 'dual'">
					              <div class="filters-query_1">
					                  <input type="number" class="form-input" :placeholder="f.placeholder"
					                      v-model="f.query_1">
					                  <small class="error-control" v-if="errors[`f.${i}.query_1`]">
					                          {{errors[`f.${i}.query_1`][0]}}
					                  </small>
					              </div>
					              <div class="filters-query_2">
					                  <input type="number" class="form-input" :placeholder="f.placeholder"
					                      v-model="f.query_2">
					                  <small class="error-control" v-if="errors[`f.${i}.query_2`]">
					                          {{errors[`f.${i}.query_2`][0]}}
					                  </small>
					              </div>
					          </template>
					          <template v-else-if="f.operator.component === 'datetime_1'">
					              <div class="filters-query_1">
					                  <input type="number" class="form-input" :placeholder="f.placeholder"
					                      v-model="f.query_1">
					                  <small class="error-control" v-if="errors[`f.${i}.query_1`]">
					                          {{errors[`f.${i}.query_1`][0]}}
					                  </small>
					              </div>
					              <div class="filters-query_2">
					                  <select class="form-input" v-model="f.query_2">
					                      <option value="hours">{{$t('hours')}}</option>
					                      <option value="days">{{$t('days')}}</option>
					                      <option value="months">{{$t('months')}}</option>
					                      <option value="years">{{$t('years')}}</option>
					                  </select>
					                  <small class="error-control" v-if="errors[`f.${i}.query_2`]">
					                          {{errors[`f.${i}.query_2`][0]}}
					                  </small>
					              </div>
					          </template>
					          <template v-else-if="f.operator.component === 'datetime_2'">
					              <div class="filters-query_2">
					                  <select class="form-input" v-model="f.query_1">
					                      <option value="yesterday">{{$t('yesterday')}}</option>
					                      <option value="today">{{$t('today')}}</option>
					                      <option value="tomorrow">{{$t('tomorrow')}}</option>
					                      <option value="last_month">{{$t('last_month')}}</option>
					                      <option value="this_month">{{$t('this_month')}}</option>
					                      <option value="next_month">{{$t('next_month')}}</option>
					                      <option value="last_year">{{$t('last_year')}}</option>
					                      <option value="this_year">{{$t('this_year')}}</option>
					                      <option value="next_year">{{$t('next_year')}}</option>
					                  </select>
					                  <small class="error-control" v-if="errors[`f.${i}.query_1`]">
					                          {{errors[`f.${i}.query_1`][0]}}
					                  </small>
					              </div>
					          </template>
					          <template v-else-if="f.operator.component === 'datetime_3'">
					              <div class="filters-query_1">
					                  <input type="date" class="form-input" :placeholder="f.placeholder"
					                      v-model="f.query_1">
					                  <small class="error-control" v-if="errors[`f.${i}.query_1`]">
					                          {{errors[`f.${i}.query_1`][0]}}
					                  </small>
					              </div>
					          </template>
					          <template v-else-if="f.operator.component === 'datetime_4'">
					              <div class="filters-query_1">
					                  <input type="number" class="form-input" :placeholder="f.placeholder"
					                      v-model="f.query_1">
					                  <small class="error-control" v-if="errors[`f.${i}.query_1`]">
					                          {{errors[`f.${i}.query_1`][0]}}
					                  </small>
					              </div>
					              <div class="filters-query_2">
					                  <select class="form-input" v-model="f.query_2">
					                      <option value="hours">{{$t('hours')}} {{$t('ago')}}</option>
					                      <option value="days">{{$t('days')}} {{$t('ago')}}</option>
					                      <option value="months">{{$t('months')}} {{$t('ago')}}</option>
					                      <option value="years">{{$t('years')}} {{$t('ago')}}</option>
					                  </select>
					                  <small class="error-control" v-if="errors[`f.${i}.query_2`]">
					                          {{errors[`f.${i}.query_2`][0]}}
					                  </small>
					              </div>
					          </template>
					          <template v-else-if="f.operator.component === 'datetime_5'">
					              <div class="filters-query_1">
					                  <input type="date" class="form-input" :placeholder="f.placeholder"
					                      v-model="f.query_1">
					                  <small class="error-control" v-if="errors[`f.${i}.query_1`]">
					                          {{errors[`f.${i}.query_1`][0]}}
					                  </small>
					              </div>
					              <div class="filters-query_2">
					                  <input type="date" class="form-input" :placeholder="f.placeholder"
					                      v-model="f.query_2">
					                  <small class="error-control" v-if="errors[`f.${i}.query_2`]">
					                          {{errors[`f.${i}.query_2`][0]}}
					                  </small>
					              </div>
					          </template>
					          <template v-else-if="f.operator.component === 'toggle'">
					              <div class="filters-query_1">
					                  <div class="filter-toggle">
					                    <x-switch v-model="f.query_1"></x-switch>
					                  </div>
					                  <small class="error-control" v-if="errors[`f.${i}.query_1`]">
					                          {{errors[`f.${i}.query_1`][0]}}
					                  </small>
					              </div>
					          </template>
					      </template>
					      <div class="filters-remove-wrap" v-if="f">
					        <button class="filters-remove" :disabled="loading" @click="removeFilter(f, i)">
					          <i class="icon icon-trash-a"></i>
					        </button>
					      </div>
					    </div>
					  </div>
					  <div class="filters-control" v-if="appliedFilters.length || showFilterControls">
					    <x-button :disabled="loading" size="sm" @click="addFilter" icon="plus"></x-button>
					    <div class="filters-control-item" v-if="appliedFilters.length">
					      <div class="filters-control-item-line"></div>
					      <x-button :disabled="loading" size="sm" @click="resetFilter" icon="refresh"></x-button>
					    </div>
					    <div class="filters-control-item">
					      <div class="filters-control-item-line"></div>
					      <x-button size="sm" type="primary" icon="funnel"
					        :disabled="loading" @click="applyFilter"></x-button>
					    </div>
					  </div>
					</div>
				</div>
			</field-tab>
			<div>
				<br>
				<label class="form-label">{{$t('preview')}}</label>
				<div class="metrics-preview">
					<div class="metrics-preview-inner">
						<value-card v-if="form.metric_type === 'value'"
							:label="form.card_label"
							:value="previewValue"></value-card>
						<chart-card v-else :label="form.card_label"
							:values="previewValues" :color="form.color"
							:type="form.chart_type"></chart-card>
					</div>
				</div>
			</div>
		</div>
		<template slot="footer">
			<div></div>
			<div>
				<x-button @click="$emit('cancel')" :disabled="loading">{{$t('cancel')}}</x-button>
				<x-button type="primary" :disabled="loading" @click="save" v-if="appliedFilters.length || showFilterControls">
					{{this.$t('add_card')}}
				</x-button>
			</div>
		</template>
	</x-modal>
</template>
<script>
	import { snakeCase } from 'lodash'
	import FieldTab from '@js/components/tabs/FieldTab.vue'
	import ChartCard from '@js/partials/ChartCard.vue'
	import ValueCard from '@js/partials/ValueCard.vue'
	export default {
		components: {
			FieldTab,
			ChartCard,
			ValueCard
		},
		data() {
			return {
				tabs: [
					{title: 'Basic', name: 'basic'},
					{title: 'Filters', name: 'filters'}
				],
				time_peroids: [
					'yesterday',
					'today',
					'last_month',
					'this_month',
					'last_year',
					'this_year'
				],
				chart_types: [
					'line',
					'bar',
					'area'
				],
				colors: [
					'#3fb1e3',
					'#6be6c1',
					'#626c91',
					'#a0a7e6',
					'#c4ebad',
					'#96dee8'
				],
				resources: [
					'contacts',
					'organizations',
					'items',
					'leads',
					'opportunities',
					'proposals',
					'contracts',
					'projects',
					'invoices',
					'payments',
					'payment_requests',
					'expenses',
					'vendors'
				],
				metrics: ['value', 'chart'],
				form: {
					resource: 'contacts',
					card_label: this.$t('card_label'),
					metric_type: 'value',
					time_peroid: 'this_month',
					chart_type: 'line',
					group_by: 'created_at',
					color: '#3fb1e3'
				},
				previewValue: 0,
				previewValues: [],
				loading: false,
				errors: {},
				query: {
				  filter_match: 'and',
				  page: 1
				},
				filterCandidates: [],
				appliedFilters: [],
			}
		},
		mounted() {
		  this.addFilter()
		},
		computed: {
			title() {
				return this.form.resource
			},
			url() {
				return this.form.resource
			},
			availableDates() {
				let resources = {
					'contacts': [
						'created_at'
					],
					'organizations': [
						'created_at'
					],
					'items': [
						'created_at'
					],
					'leads': [
						'created_at'
					],
					'opportunities': [
						'start_date',
						'close_date',
						'created_at'
					],
					'proposals': [
						'issue_date',
						'expiry_date',
						'created_at'
					],
					'contracts': [
						'issue_date',
						'expiry_date',
						'created_at'
					],
					'projects': [
						'start_date',
						'estimated_finish_date',
						'deadline_date',
						'actual_start_date',
						'actual_end_date',
						'created_at'
					],
					'invoices': [
						'issue_date',
						'due_date',
						'created_at'
					],
					'payments': [
						'payment_date',
						'created_at'
					],
					'payment_requests': [
						'expiry_at',
						'created_at'
					],
					'expenses': [
						'created_at'
					],
					'vendors': [
						'created_at'
					]
				}

				return resources[this.form.resource]
			},
			filterGroup() {
				let resources = {
					'contacts':[{
			        title: this.$t('contact'),
			        filters: [
			            {name: 'id', type: 'numeric'},
			            {name: 'number', type: 'lookup', resource: 'contacts'},
			            {name: 'name', type: 'lookup', resource: 'contacts'},
			            {name: 'title', type: 'string'},
			            {name: 'department', type: 'string'},
			            {name: 'email', type: 'lookup', resource: 'contacts'},
			            {name: 'website', type: 'lookup', resource: 'contacts'},
			            {name: 'fax', type: 'lookup', resource: 'contacts'},
			            {name: 'phone', type: 'lookup', resource: 'contacts'},
			            {name: 'mobile', type: 'lookup', resource: 'contacts'},
			            {name: 'primary_address', type: 'string'},
			            {name: 'other_address', type: 'string'},
			            {name: 'total_revenue', type: 'numeric'},
			            {name: 'last_contact', type: 'datetime'},
			            {name: 'last_project', type: 'datetime'},
			            {name: 'last_invoice', type: 'datetime'},
			            {name: 'last_payment', type: 'datetime'},
			            {name: 'last_proposal', type: 'datetime'},
			            {name: 'created_at', type: 'datetime'},
			        ]
			    }, {
			    	title: this.$t('organization'),
              filters: [
                {name: 'organization.id', type: 'numeric'},
                {name: 'organization.number', type: 'lookup', resource: 'organizations', column: 'number'},
                {name: 'organization.name', type: 'lookup', resource: 'organizations', column: 'name'},
                {name: 'organization.created_at', type: 'datetime'},
              ]
			    }],
					'organizations': [{
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
			    }],
					'items': [{
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
				    }],
					'leads': [{
			        title: this.$t('lead'),
			        filters: [
			            {name: 'id', type: 'numeric'},
			            {name: 'number', type: 'lookup', resource: 'leads'},
			            {name: 'person', type: 'lookup', resource: 'leads'},
			            {name: 'organization', type: 'lookup', resource: 'leads'},
			            {name: 'title', type: 'string'},
			            {name: 'department', type: 'string'},
			            {name: 'email', type: 'lookup', resource: 'leads'},
			            {name: 'website', type: 'lookup', resource: 'leads'},
			            {name: 'fax', type: 'lookup', resource: 'leads'},
			            {name: 'phone', type: 'lookup', resource: 'leads'},
			            {name: 'mobile', type: 'lookup', resource: 'leads'},
			            {name: 'primary_address', type: 'string'},
			            {name: 'other_address', type: 'string'},
			            {name: 'created_at', type: 'datetime'},
			        ]
			    }, {
			    	title: this.$t('status'),
              filters: [
                {name: 'status.name', type: 'lookup', resource: 'lead_statuses', column: 'name'},
              ]
			    }],
					'opportunities': [{
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
			    }],
					'proposals': [{
			        title: this.$t('proposal'),
			        filters: [
			            {name: 'id', type: 'numeric'},
			            {name: 'number', type: 'lookup', resource: 'proposals'},
			            {name: 'issue_date', type: 'datetime'},
			            {name: 'expiry_date', type: 'datetime'},
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
			    	title: this.$t('opportunity'),
              filters: [
                {name: 'opportunity.number', type: 'lookup', resource: 'opportunities', column: 'number'},
                {name: 'opportunity.created_at', type: 'datetime'},
              ]
			    },{
			    	title: this.$t('status'),
              filters: [
                {name: 'status.name', type: 'lookup', resource: 'proposal_statuses', column: 'name'}
              ]
			    }],
					'contracts': [{
			        title: this.$t('contract'),
			        filters: [
			            {name: 'id', type: 'numeric'},
			            {name: 'number', type: 'lookup', resource: 'contracts'},
			            {name: 'title', type: 'lookup', resource: 'contracts'},
			            {name: 'start_date', type: 'datetime'},
			            {name: 'expiry_date', type: 'datetime'},
			            {name: 'value', type: 'numeric'},
			            {name: 'auto_renewal', type: 'toggle'},
			            {name: 'no_of_months', type: 'numeric'},
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
			    	title: this.$t('type'),
              filters: [
                {name: 'type.name', type: 'lookup', resource: 'contract_types', column: 'name'}
              ]
			    },{
			    	title: this.$t('status'),
              filters: [
                {name: 'status.name', type: 'lookup', resource: 'contract_statuses', column: 'name'}
              ]
			    }],
					'projects': [{
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
			    }],
					'invoices': [{
			        title: this.$t('invoice'),
			        filters: [
			            {name: 'id', type: 'numeric'},
			            {name: 'number', type: 'lookup', resource: 'invoices'},
			            {name: 'reference', type: 'string'},
			            {name: 'issue_date', type: 'datetime'},
			            {name: 'due_date', type: 'datetime'},
			            {name: 'sub_total', type: 'numeric'},
			            {name: 'grand_total', type: 'numeric'},
			            {name: 'amount_paid', type: 'numeric'},
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
			    	title: this.$t('contract'),
              filters: [
                {name: 'contract.number', type: 'lookup', resource: 'contracts', column: 'number'},
                {name: 'contract.created_at', type: 'datetime'},
              ]
			    },{
			    	title: this.$t('status'),
              filters: [
                {name: 'status.name', type: 'lookup', resource: 'invoice_statuses', column: 'name'}
              ]
			    }],
					'payments': [{
			        title: this.$t('payment'),
			      	filters: [
			      	    {name: 'id', type: 'numeric'},
			      	    {name: 'number', type: 'lookup', resource: 'contracts'},
			      	    {name: 'reference', type: 'string'},
			      	    {name: 'payment_date', type: 'datetime'},
			      	    {name: 'amount_received', type: 'numeric'},
			      	    {name: 'amount_applied', type: 'numeric'},
			      	    {name: 'note', type: 'string'},
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
			    	title: this.$t('deposit'),
              filters: [
                {name: 'deposit.name', type: 'lookup', resource: 'payment_deposits', column: 'name'}
              ]
			    },{
			    	title: this.$t('method'),
              filters: [
                {name: 'method.name', type: 'lookup', resource: 'payment_methods', column: 'name'}
              ]
			    }],
					'payment_requests': [{
			        title: this.$t('payment_request'),
			        filters: [
			            {name: 'id', type: 'numeric'},
			            {name: 'number', type: 'lookup', resource: 'payment_requests'},
			            {name: 'email', type: 'string'},
			            {name: 'uuid', type: 'string'},
			            {name: 'payment_received_at', type: 'datetime'},
			            {name: 'expiry_at', type: 'datetime'},
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
			    	title: this.$t('invoice'),
              filters: [
                {name: 'invoice.number', type: 'lookup', resource: 'invoices', column: 'number'},
                {name: 'invoice.created_at', type: 'datetime'},
              ]
			    }],
					'expenses': [{
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
			    }],
					'vendors': [{
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
				}

				return resources[this.form.resource]
			},
			setOptions() {
			  return (items) => {
			    return items.map((item) => {
			      return {name: item, value: this.$t(item)}
			    })
			  }
			},
			showFilterReset() {
			  return this.appliedFilters.length > 0
			},
			showFilterReset() {
			  return this.appliedFilters.length > 0
			},
			showFilterControls() {
			  if(this.filterCandidates.length === 0) return true
			  const active = this.filterCandidates.filter((f) => {
			    if(f.column && f.column.name) {
			      return f
			    }
			  })

			  return active.length > 0
			},
			filterGroups() {
        const x = this.filterGroup.map((item) => {
          const i = item.filters.map((y) => {
            if(typeof y.title === 'undefined') {
              y.title = this.$t(y.name)
              return y
            }
            return y
          })
          item.filters = i
          return item
        })

        return x
      },
      fetchOperators() {
        return (f) => {
          return this.availableOperator().filter((operator) => {
            if(f.column && operator.parent.includes(f.column.type)) {
              return operator
            }
          })
        }
      }
		},
		methods: {
			 save() {
          this.errors = {}
          this.loading = true
          const f = []
          this.appliedFilters.forEach((filter, i) => {
              const c = {
                  column: filter.column.name,
                  operator: filter.operator.name,
                  query_2: filter.query_2
              }
              // console.log(Array.isArray(filter.query_1))
              if(filter.query_1 && Array.isArray(filter.query_1)) {
                const list = filter.query_1.map((item) => {
                  return item.value
                })

                c.query_1 = list.join(',,')
              } else {
                c.query_1 = filter.query_1
              }

              f.push(c)
          })

          let form = {}
          if(f.length > 0) {
          	form = {
          	    ...this.query,
          	    ...this.form,
          	    f
          	}
          } else {
          	form = {
	              ...this.query,
	              ...this.form
	          }
          }

          this.$http.post(`/api/user_metrics`, form)
              .then(res => {
                  if(res.data.saved) {
                  	const id = Math.random().toString(36).substring(7)
										this.$router.push(`/?id=${id}`)
                    this.$message.success(`${this.$t('saved_success')}`)
                    this.$emit('cancel')
                  }
              })
              .catch(error => {
                  this.loading = false
                  if(error.response.status === 422) {
                      this.errors = error.response.data.errors
                      this.$message.error(error.response.data.message)
                  }
              })
      },
			fetch() {
			  this.loading = true
			  this.errors = {}

			  const params = {
			  	...this.getQuery(),
			  	metric_type: this.form.metric_type,
			  	chart_type: this.form.chart_type,
			  	time_peroid: this.form.time_peroid,
			  	group_by: this.form.group_by
			  }

			  this.$http.get(`/api/${this.url}`, { params: params })
			    .then((res) => {
			      this.setData(res)
			    })
			    .catch((error) => {
			      this.$bar.finish()
			      this.loading = false
			      if(error.response.status === 422) {
			          this.errors = error.response.data.errors
			          this.$message.error(error.response.data.message)
			      }
			    })
			},
			setData(res) {
        // this.$set(this.$data, 'collection', res.data.collection)
        // this.query.page = this.collection.current_page
        // this.query.limit = Number(this.collection.per_page)
        if(this.form.metric_type === 'chart') {
        	this.previewValues = res.data.collection
        } else {
        	this.previewValue = res.data.collection
        }
        this.loading = false
      },
			resetFilter() {
			    this.$set(this.$data, 'appliedFilters', [])
			    this.filterCandidates.splice(0)
			    this.addFilter()
			    this.query.page = 1
			    this.fetch()
			},
			applyFilter() {
			    this.$set(this.$data, 'appliedFilters',
			        JSON.parse(JSON.stringify(this.filterCandidates))
			    )
			    this.query.page = 1
			    this.fetch()
			},
			onColumnSelect(f, i, e) {
			  const value = event.target.value
			  if(value.length === 0) {
			    this.$set(this.filterCandidates[i], 'column', value)
			    return
			  }
			  const obj = JSON.parse(value)
			  this.$set(this.filterCandidates[i], 'column', obj)

			  switch(obj.type) {
			    case 'numeric':
			      this.filterCandidates[i].operator = this.availableOperator()[6]
			      this.filterCandidates[i].query_1 = null
			      this.filterCandidates[i].query_2 = null
			      break;
			    case 'lookup':
			    case 'lookup_only':
			      this.filterCandidates[i].operator = this.availableOperator()[11]
			      this.filterCandidates[i].query_1 = []
			      this.filterCandidates[i].query_2 = null
			      break;

			    case 'static_lookup':
			      this.filterCandidates[i].operator = this.availableOperator()[21]
			      this.filterCandidates[i].query_1 = []
			      this.filterCandidates[i].query_2 = null
			      break;
			    case 'string':
			      this.filterCandidates[i].operator = this.availableOperator()[8]
			      this.filterCandidates[i].query_1 = []
			      this.filterCandidates[i].query_2 = null
			      break;
			    case 'toggle':
			      this.filterCandidates[i].operator = this.availableOperator()[23]
			      this.filterCandidates[i].query_1 = 1
			      this.filterCandidates[i].query_2 = null
			      break;
			    case 'datetime':
			      this.filterCandidates[i].operator = this.availableOperator()[13]
			      this.filterCandidates[i].query_1 = 28
			      this.filterCandidates[i].query_2 = 'days'
			      break;
			  }
			},
			onOperatorSelect(f, i, e) {
			  // console.log('operator change')
			  const value = event.target.value
			  if(value.length === 0) {
			    this.$set(this.filterCandidates[i], 'operator', value)
			    return
			  }

			  const obj = JSON.parse(value)
			  this.$set(this.filterCandidates[i], 'operator', obj)

			  this.filterCandidates[i].query_1 = null
			  this.filterCandidates[i].query_2 = null
			  // set default values for query_1 and q2
			  switch(obj.name) {
			    case 'includes':
			    case 'not_includes':
			      this.filterCandidates[i].query_1 = []
			      this.filterCandidates[i].query_2 = null
			      break;
			    case 'in_the_past':
			    case 'in_the_next':
			    case 'over':
			      this.filterCandidates[i].query_1 = 28
			      this.filterCandidates[i].query_2 = 'days'
			      break;
			    case 'in_the_peroid':
			      this.filterCandidates[i].query_1 = 'today'
			      break;
			    case 'toggle':
			      this.filterCandidates[i].query_1 = 1
			      break;
			  }
			},
			addFilter() {
			    this.filterCandidates.push({
			        column: '',
			        operator: '',
			        query_1: null,
			        query_2: null,
			        placeholder: ''
			    })
			},
			removeFilter(f, i) {
			    this.filterCandidates.splice(i, 1)
			},
			getQuery() {
        const f = {}

        this.appliedFilters.forEach((filter, i) => {
            f[`f[${i}][column]`] = filter.column.name
            f[`f[${i}][operator]`] = filter.operator.name

            if(filter.query_1 && Array.isArray(filter.query_1)) {
              const list = filter.query_1.map((item) => {
                return item.value
              })

              f[`f[${i}][query_1]`] = list.join(',,')
            } else {
              f[`f[${i}][query_1]`] = filter.query_1
            }

            f[`f[${i}][query_2]`] = filter.query_2
        })

        let params = {
            ...this.query,
            ...f
        }

        return params
      },
      availableOperator() {
          return [
              {name: 'equal_to', parent: ['numeric', 'string'], component: 'single'},
              {name: 'not_equal_to', parent: ['numeric', 'string'], component: 'single'},
              {name: 'less_than', parent: ['numeric'], component: 'single'},
              {name: 'greater_than', parent: ['numeric'], component: 'single'},
              {name: 'less_than_or_equal_to', parent: ['numeric'], component: 'single'},
              {name: 'greater_than_or_equal_to', parent: ['numeric'], component: 'single'},
              {name: 'between', parent: ['numeric'], component: 'dual'},
              {name: 'not_between', parent: ['numeric'], component: 'dual'},
              {name: 'contains', parent: ['string', 'lookup'], component: 'single'},
              {name: 'starts_with', parent: ['string', 'lookup'], component: 'single'},
              {name: 'ends_with', parent: ['string', 'lookup'], component: 'single'},

              {name: 'includes', parent: ['lookup', 'lookup_only'], component: 'lookup'},
              {name: 'not_includes', parent: ['lookup', 'lookup_only'], component: 'lookup'},
              {name: 'in_the_past', parent: ['datetime'], component: 'datetime_1'},
              {name: 'in_the_next', parent: ['datetime'], component: 'datetime_1'},
              {name: 'over', parent: ['datetime'], component: 'datetime_4'}, // same as in_the_past
              {name: 'between_date', parent: ['datetime'], component: 'datetime_5'},
              {name: 'in_the_peroid', parent: ['datetime'], component: 'datetime_2'},
              {name: 'equal_to_date', parent: ['datetime'], component: 'datetime_3'},
              {name: 'is_empty', parent: ['date', 'numeric', 'string', 'datetime', 'lookup'], component: 'none'},
              {name: 'is_not_empty', parent: ['date', 'numeric', 'string', 'datetime', 'lookup'], component: 'none'},
              {name: 'includes', parent: ['static_lookup'], component: 'static_lookup'},
              {name: 'not_includes', parent: ['static_lookup'], component: 'static_lookup'},
              {name: 'toggle', parent: ['toggle'], component: 'toggle'},
          ]
      }
		}
	}
</script>