<template>
  <div class="filterable">
    <x-panel>
      <div slot="title">
        <div class="filters-title">
            <span>{{$t(title)}} {{$t('match')}}</span>
            <select v-model="query.filter_match" class="form-input form-input-sm">
                <option value="and">{{$t('all')}}</option>
                <option value="or">{{$t('any')}}</option>
            </select>
            <span>{{$t('of_the_following')}}</span>
        </div>
      </div>
      <div slot="extra">
        <slot name="extra"></slot>
      </div>
      <div>
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
    </x-panel>
    <div class="filterable-mid">
      <div>
        <exportable :disabled="!exportable || loading" :total="collection.total"
                :applied-filters="appliedFilters" :query="query" :resource="url"></exportable>
      </div>
      <div>
        <span>{{$t('order_by')}}</span>
        <select class="filterable-sort form-input form-input-sm"
            v-model="query.sort_column" :disabled="loading"
            @input="handleColumnSort">
            <option v-for="column in getSortable" :value="column.name">
                {{column.value}}
            </option>
        </select>
        <strong class="filterable-direction" @click="onDirectionSort">
          <i class="icon icon-arrow-down-c" v-if="query.sort_direction === 'desc'"></i>
          <i class="icon icon-arrow-up-c" v-else></i>
        </strong>
      </div>
    </div>
    <x-panel :loading="loading">
      <x-table class="table table-link">
        <thead>
          <slot name="heading"></slot>
        </thead>
        <x-tbody v-if="collection.data && collection.data.length">
          <slot v-for="item in collection.data" :item="item"></slot>
        </x-tbody>
        <x-tbody v-else>
          <x-tr>
            <x-td colspan="10">
              <div class="table-no_results">{{$t('no_results_found')}}</div>
            </x-td>
          </x-tr>
        </x-tbody>
      </x-table>
      <div class="flex flex-between flex-center" slot="footer">
        <div>
            <select class="form-input form-input-sm" @input="updateLimit"
              :disabled="loading">
                <option :selected="query.limit === 10" value="10">10</option>
                <option :selected="query.limit === 15" value="15">15</option>
                <option :selected="query.limit === 25" value="25">25</option>
                <option :selected="query.limit === 50" value="50">50</option>
                <option :selected="query.limit === 100" value="100">100</option>
            </select>
            <small>{{$t('showing', {from: collection.from || 0, to: collection.to || 0, total: collection.total})}}</small>
        </div>
        <div>
          <x-button size="sm" :disabled="!collection.prev_page_url || loading"
            @click="prevPage">
            &laquo; {{$t('prev')}}
          </x-button>
          <x-button size="sm" :disabled="!collection.next_page_url || loading"
            @click="nextPage">
            {{$t('next')}} &raquo;
          </x-button>
        </div>
      </div>
    </x-panel>
  </div>
</template>
<script>
  import Exportable from '../exportable/Exportable.vue'
  export default {
    components: { Exportable },
    name: 'XFilterable',
    props: {
      title: String,
      url: String,
      sortable: Array,
      filterGroup: Array,
      exportable: {
        type: Boolean,
        default: true
      }
    },
    data() {
      return {
        loading: true,
        collection: {
          data: []
        },
        errors: {},
        query: {
          sort_column: 'created_at',
          sort_direction: 'desc',
          filter_match: 'and',
          limit: 10,
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
      showFilterControls() {
        if(this.filterCandidates.length === 0) return true
        const active = this.filterCandidates.filter((f) => {
          if(f.column && f.column.name) {
            return f
          }
        })

        return active.length > 0
      },
      getSortable() {
        const x = this.sortable.map((item) => {
          if(typeof item === 'object') {
            return item
          }
          return {name: item, value: this.$t(item)}
        })

        return x
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
      fetch() {
        this.loading = true
        this.errors = {}

        const params = this.getQuery()

        this.$http.get(`/api/${this.url}`, { params: params })
          .then((res) => {
            this.setData(res)
          })
          .catch((error) => {
            this.$bar.finish()
            this.loading = false
            if(error.response.status === 422) {
                this.errors = error.response.data.errors
            }
            this.$message.error(error.response.data.message)
          })
      },
      setData(res) {
        this.$set(this.$data, 'collection', res.data.collection)
        this.query.page = this.collection.current_page
        this.query.limit = Number(this.collection.per_page)
        this.loading = false
      },
      nextPage () {
          if(this.collection.next_page_url) {
              this.query.page = this.query.page + 1
              this.fetch()
          }
      },
      prevPage() {
          if(this.collection.prev_page_url) {
              this.query.page = this.query.page - 1
              this.fetch()
          }
      },
      updateLimit(e) {
        this.query.limit = Number(e.target.value)
        this.fetch()
      },
      handleColumnSort(event) {
          this.query.sort_column = event.target.value
          this.query.sort_direction = 'asc'
          this.fetch()
      },
      onDirectionSort() {
          // hack
          if(this.loading) return

          if(this.query.sort_direction === 'desc') {
              this.query.sort_direction = 'asc'
          } else {
              this.query.sort_direction = 'desc'
          }
          this.fetch()
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