<template>
	<div class="content-inner">
		<x-filterable :exportable="false" :url="url" :sortable="sortable" :filter-group="filters"
			ref="filterable" :title="title">
			<div slot="extra">
				<x-button type="primary" @click="toggleUploadModal">{{$t('upload')}}</x-button>
			</div>
			<x-tr slot="heading">
			    <x-td type="th" size="2">{{$t('id')}}</x-td>
			    <x-td type="th" size="2">{{$t('image')}}</x-td>
			    <x-td type="th" size="12">{{$t('title')}}</x-td>
			    <x-td type="th" size="3">{{$t('size')}}</x-td>
			    <x-td type="th" size="5" colspan="2">{{$t('created_at')}}</x-td>
			</x-tr>
			<x-tr slot-scope="{ item }" @click.native="showPreview(item)">
			    <x-td>{{item.id}}</x-td>
			    <x-td>
			    	<img :src="item.filename" class="index-thumb">
			    </x-td>
			    <x-td>{{item.title}}</x-td>
			    <x-td>{{item.size}}</x-td>
			    <x-td>{{item.created_at | toDate}}</x-td>
			    <x-td>
			    	<x-button @click.stop="remove(item)" type="error" size="sm" icon="trash-b"></x-button>
			    </x-td>
			</x-tr>
		</x-filterable>
		<x-modal width="900" :footer="false" v-if="showModal" @cancel="onClose">
			<div slot="title">{{$t('image')}} / {{selected.title}}</div>
			<div class="image-preview">
				<div class="image-holder">
					<div class="image-holder-inner">
						<img :src="selected.filename" class="image-thumb">
					</div>
				</div>
				<div class="image-form">
					<x-row>
						<x-col span="8">
							<div class="form-group">
								<label class="form-label">{{$t('extension')}}</label>
								<p>{{selected.extension}}</p>
							</div>
						</x-col>
						<x-col span="8">
							<div class="form-group">
								<label class="form-label">{{$t('size')}}</label>
								<p>{{selected.size}}</p>
							</div>
						</x-col>
						<x-col span="8">
							<div class="form-group">
								<label class="form-label">{{$t('dimension')}}</label>
								<p>{{selected.dimension}}</p>
							</div>
						</x-col>
					</x-row>
					<div class="form-group">
						<label class="form-label">{{$t('filename')}}</label>
						<p>{{selected.filename}}</p>
					</div>
					<br>
					<x-form-group v-model="form.title" :label="$t('title')" :errors="errors.title"></x-form-group>
					<x-button size="sm" type="primary" :loading="isSaving" @click="saveTitle(form)">{{$t('save')}}</x-button>
				</div>
			</div>
		</x-modal>
		<upload v-if="showUploadModal" @cancel="onUploadClose" @saved="onSaved"></upload>
	</div>
</template>
<script>
	import { indexable } from '@js/lib/mixins'
	import upload from '@js/views/images/upload.vue'

	export default {
		mixins: [indexable],
		components: { upload },
		data() {
			return {
				url: 'images',
				title: 'media_library',
				selected: null,
				showModal: false,
				form: {
					title: null
				},
				errors: {},
				isSaving: false,
				showUploadModal: false
			}
		},
		computed: {
			sortable() {
				let columns = [
				  'id', 'title', 'size', 'extension',
				  'created_at'
				]
				return columns
			},
			filters() {
			    let groups = [{
			        title: this.$t('media_library'),
			        filters: [
			            {name: 'id', type: 'numeric'},
			            {name: 'title', type: 'string'},
			            {name: 'extension', type: 'string'},
			            {name: 'size', type: 'numeric'},
			            {name: 'dimension', type: 'string'},
			            {name: 'created_at', type: 'datetime'},
			        ]
			    }]

			    return groups
			}
		},
		methods: {
			remove(item) {
        const r = confirm(this.$t('are_you_sure'))
        if(r != true) { return }
        this.loading = true

        this.$http.delete(`/api/images/${item.id}`)
          .then((res) => {
            if(res.data.deleted) {
              this.$message.success(this.$t('success_delete'))
              const id = Math.random().toString(36).substring(7)
              this.$router.push(`/media_library?id=${id}`)
            }
          })
          .catch((error) => {
            this.loading = false
            if(error.response.status === 422) {
                this.errors = error.response.data.errors
                this.$message.error(error.response.data.message)
            }
          })
      },
			onSaved(e) {
				this.onUploadClose()
				this.$router.push(`/media_library?q=${e.str_rand}`)
			},
			showPreview(item) {
				this.selected = item
				this.form.title = item.title
				this.showModal = true
			},
			onClose() {
				this.selected = null
				this.showModal = false
			},
			saveTitle(form) {
				this.errors = {}
				this.isSaving = true
				this.$http.put(`/api/images/${this.selected.id}`, form)
					.then((res) => {
						this.selected.title = form.title
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
			toggleUploadModal() {
				this.showUploadModal = true
			},
			onUploadClose() {
				this.showUploadModal = false
			}
		}
	}
</script>