<template>
  <div class="filterable">
    <x-panel :loading="loading" margin alt>
      <div slot="title">
        <div class="filters-title">
            <span>{{$t(title)}}</span>
        </div>
      </div>
      <div slot="extra">
      	<x-button size="sm" type="success" @click="toggleModal">{{$t('add')}}</x-button>
      </div>
      <x-table class="table">
        <thead>
          <slot name="heading"></slot>
        </thead>
        <x-tbody v-if="collection.data && collection.data.length">
          <slot v-for="item in collection.data" :item="item" :remove="remove" :edit="edit"></slot>
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
	  <x-modal width="450" :loading="isSaving" @cancel="onCancel" :okText="$t('save')" @ok="save" v-if="showModal">
	  	<div slot="title">
	  		<div v-if="isEdit">
	  			<span>{{$t('edit')}} {{$t('tasks')}}</span>
	  		</div>
	  		<div v-else>{{$t('add')}} {{$t('tasks')}}</div>
	  	</div>
	  	<x-form-group v-model="form.title" :label="$t('title')" :errors="errors.title"></x-form-group>
      <x-row>
        <x-form-group type="date" col="12" v-model="form.start_date"
          :label="$t('start_date')" :errors="errors.start_date"></x-form-group>
        <x-form-group type="date" col="12" v-model="form.due_date"
          :label="$t('due_date')" :errors="errors.due_date"></x-form-group>
      </x-row>
      <x-form-group source="textarea" v-model="form.description" :label="$t('description')" :errors="errors.description"></x-form-group>
      <x-form-group :label="$t('stage_id')" :errors="errors.stage_id">
        <x-tag-input :value="form.stage" resource="project_stages" column="name" name="name"
            @input="value => { form.stage_id = value.id; form.stage = value }">
        </x-tag-input>
      </x-form-group>
	  </x-modal>
  </div>
</template>
<script>
  export default {
    name: 'ProjectTasks',
    props: {
      title: String,
      url: String,
      type: String,
      project: Number
    },
    data() {
      return {
      	isSaving: false,
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
        form: {
        	title: '',
          start_date: null,
          due_date: null,
          description: '',
          stage_id: null,
          stage: null
        },
        id: null,
        showModal: false,
        isEdit: false
      }
    },
    mounted() {
      this.fetch()
    },
    methods: {
    	getForm() {
    		let r = this.url
    		let id = this.id
    		let url = `/api/${r}?project_id=${this.project}`
    		let method = 'post'

    		if(this.isEdit) {
    			url = `/api/${r}/${id}?project_id=${this.project}`
    			method = 'put'
    		}

    		return {
    			url,
    			method
    		}
    	},
    	save() {
    		this.isSaving = true
    		this.errors = {}

    		const { url, method } = this.getForm()

    		this.$http[method](url, this.form)
    			.then((res) => {
    				this.showModal = false
    				this.fetch()
    				this.$message.success(this.$t('saved_success'))
    			})
    			.catch((error) => {
    				if(error.response.status === 422) {
    				    this.errors = error.response.data.errors
    				}
    				this.$message.error(error.response.data.message)
    			})
    			.finally(() => {
    				this.isSaving = false
    			})
    	},
    	toggleModal() {
    		if(!this.showModal) {
    			this.$set(this.form, 'name', null)
    		}
    		this.showModal = !this.showModal
    	},
    	onCancel() {
    		this.showModal = false
    		this.isEdit = false
    	},
    	edit(item) {
    		this.isEdit = true
    		this.id = item.id
    		this.$set(this.$data, 'form', item)
    		this.showModal = true
    	},
      remove(item) {
        const r = confirm(this.$t('are_you_sure'))
        if(r != true) { return }
        this.loading = true

        this.$http.delete(`/api/${this.url}/${item.id}?project_id=${this.project}`)
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

        this.$http.get(`/api/${this.url}?project_id=${this.project}`, { params: params })
          .then((res) => {
            this.setData(res)
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