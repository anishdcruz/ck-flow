<template>
	<x-panel :loading="isSaving || loading" margin>
		<div slot="title">{{$t('custom_fields')}}</div>
		<div slot="extra">
			<x-button size="sm" type="success" @click="toggleFieldModal">{{$t('add_field')}}</x-button>
		</div>
		<div>
			<div class="fields" v-if="form.fields.length > 0">
	      <div :class="[`field field-${field.width}`]" v-for="(field, fIndex) in form.fields">
	        <field-item
	        	:page="title"
	        	:field="field"
	        	@remove-field="handleRemoveField(fIndex, form.fields)"
	        	@update="handleUpdateField(fIndex, form.fields, $event)"
	        	/>
	      </div>
			</div>
			<div v-else>
				<div class="table-no_results">{{$t('no_custom_fields')}}</div>
			</div>
		</div>
		<div slot="footer" class="flex flex-end">
			<div>
				<x-button @click="save" type="primary" :loading="isSaving">{{$t('save')}}</x-button>
			</div>
		</div>
    <field-type-modal v-if="showFieldModal" @cancel="toggleFieldModal"
      @add="handleAddField"></field-type-modal>
	</x-panel>
</template>
<script>
	import FieldItem from '@js/partials/CustomFieldItem.vue'
	import FieldTypeModal from '@js/partials/CustomFieldModal.vue'
	import MiniCrud from '@js/partials/MiniCrud.vue'
	export default {
		components: { FieldItem, FieldTypeModal, MiniCrud },
		props: {
			title: String,
      url: String,
		},
		data() {
			return {
				form: {
					fields: []
				},
				errors: {},
				isSaving: false,
        loading: true,
        showFieldModal: false
			}
		},
		mounted() {
      this.fetch()
    },
		methods: {
			handleUpdateField(i, fields, e) {
			   const f = e.target.value
			   this.$set(this.form.fields, i, f)
			},
			handleRemoveField(index, fields) {
	      const r = confirm(this.$t('are_you_sure'))
        if(r != true) { return }
        fields.splice(index, 1)
      },
      handleAddField(e) {
        const f = e.target.value
        this.form.fields.push(f)
        this.toggleFieldModal();
      },
      toggleFieldModal() {
        this.showFieldModal = !this.showFieldModal
      },
      getForm() {
    		let r = this.url
    		let id = this.id
    		let url = `/api/settings/custom_fields?type=${this.title}`
    		let method = 'post'

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
    				// this.fetch()
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
      fetch() {
        this.loading = true
        this.errors = {}

        let params = {
          type: this.type,
          id: this.id,
          ...this.query
        }

        this.$http.get(`/api/settings/custom_fields?type=${this.title}`, { params: params })
          .then((res) => {
            this.setData(res)
          })
      },
      setData(res) {
        this.$set(this.$data, 'form', res.data.form)
        this.loading = false
      },
		}
	}
</script>