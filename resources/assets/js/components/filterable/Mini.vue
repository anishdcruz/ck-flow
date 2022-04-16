<template>
  <div class="filterable">
    <x-panel :loading="loading" margin alt>
      <div slot="title">
        <div class="filters-title">
            <span>{{$t(title)}}</span>
        </div>
      </div>
      <div slot="extra">
        <slot name="extra"></slot>
      </div>
      <x-table class="table table-link">
        <thead>
          <slot name="heading"></slot>
        </thead>
        <x-tbody v-if="collection.data && collection.data.length">
          <slot v-for="item in collection.data" :item="item" :remove="remove"></slot>
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
                <option :selected="query.limit === 5" value="5">5</option>
                <option :selected="query.limit === 15" value="15">15</option>
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
  export default {
    name: 'XMini',
    props: {
      title: String,
      url: String,
      type: String,
      id: Number
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
          limit: 5,
          page: 1
        },
      }
    },
    mounted() {
      this.fetch()
    },
    methods: {
      remove(item) {
        const r = confirm(this.$t('are_you_sure'))
        if(r != true) { return }
        this.loading = true

        this.$http.delete(`/api/${this.url}/${item.id}`)
          .then((res) => {
            if(res.data.deleted) {
              this.$message.success(this.$t('success_delete'))
              this.fetch()
            }
          })
      },
      fetch() {
        this.loading = true
        this.errors = {}

        let params = {
          type: this.type,
          id: this.id,
          ...this.query
        }

        this.$http.get(`/api/${this.url}`, { params: params })
          .then((res) => {
            this.setData(res)
          })
          .catch(err => {
            this.loading = false
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
      }
    }
  }
</script>