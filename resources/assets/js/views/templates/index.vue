<template>
	<div class="content-inner">
		<x-filterable :exportable="false" :url="url" :sortable="sortable" :filter-group="filters"
			ref="filterable" :title="title">
			<div slot="extra">
				<x-button type="secondary" @click="toggleImport">
					{{$t('import')}}
				</x-button>
				<router-link to="/templates/create" class="btn btn-primary">
					{{$t('new_btn', {type: $t('template')})}}
				</router-link>
			</div>
			<x-tr slot="heading">
			    <x-td type="th" size="3">{{$t('id')}}</x-td>
			    <x-td type="th" size="4">{{$t('type')}}</x-td>
			    <x-td type="th" size="14">{{$t('name')}}</x-td>
			    <x-td type="th" size="3">{{$t('created_at')}}</x-td>
			</x-tr>
			<x-tr slot-scope="{ item }" @click.native="$router.push(`/templates/${item.id}`)">
			    <x-td>{{item.id}}</x-td>
			    <x-td>{{item.type}}</x-td>
			    <x-td>{{item.name | trim(70)}}</x-td>
			    <x-td>{{item.created_at | toDate}}</x-td>
			</x-tr>
		</x-filterable>
		<x-modal width="450" v-if="showImport" @cancel="toggleImport">
			<div slot="title">
				{{$t('import_template')}}
			</div>
					<div class="upload">
						<div v-if="files && files.length > 0">
							<template v-for="(file, index) in files">
								<x-row class="upload-item">
									<x-col span="20">
										<strong>{{file.name}}</strong>
										<br>
				          	<small>({{file.size}})</small>
				          	<br>
				          	<small class="error-input" v-if="errors && errors[`images.${index}`]">
				          	  {{errors[`images.${index}`][0]}}
				          	</small>
									</x-col>
								</x-row>
							</template>
						</div>
						<div class="upload-inner" @dragover.prevent="onDragOver"
			      	@drop.prevent="onDrop" v-else>
							<div class="upload-icon">
								<span class="icon icon-folder"></span>
							</div>
							<p class="upload-text">{{$t('drag_and_drop_zip')}}</p>
							<div class="upload-browse">
								<x-button size="sm" @click="onBrowse">{{$t('browse_zip')}}</x-button>
								<input v-show="false" ref="input"
			        		type="file" @change="handleUpload" multiple>
							</div>
						</div>
					</div>
		</x-modal>
	</div>
</template>
<script>
	import { indexable } from '@js/lib/mixins'
	export default {
		mixins: [indexable],
		data() {
			return {
				url: 'templates',
				title: 'templates',
				showImport: false,
				isUploading: false,
				files: [],
				errors: {}
			}
		},
		computed: {
			sortable() {
				let columns = [
				  'id', 'title', 'created_at'
				]
				return columns
			},
			filters() {
			    let groups = [{
			        title: this.$t('template'),
			        filters: [
			            {name: 'id', type: 'numeric'},
			            {name: 'name', type: 'string'},
			            {name: 'created_at', type: 'datetime'},
			        ]
			    }]

			    return groups
			}
		},
		methods: {
			toggleImport() {
				this.showImport = !this.showImport
			},
			onDragOver() {},
			onDrop(e) {
			  const files = e.dataTransfer.files
			  for(var i = 0; i< files.length; i++) {
			  	if (files[i].name.match(/\.zip/)) {
			  		this.files.push(files[i])
			  	}
			  }

			  this.postUpload()
			},
			handleUpload(e) {
        const files = e.target.files
        for(var i = 0; i< files.length; i++) {
			  	if (files[i].name.match(/\.zip/)) {
			  		this.files.push(files[i])
			  	}
			  }
        this.postUpload()
      },
      onBrowse() {
			  this.$refs.input.click()
			},
			postUpload() {
			  var fd = new FormData()
			  fd.append(`template`, this.files[0])
			  this.save(fd)
			},
			save(fd) {
			  this.isUploading = true
			  this.$http.post('/api/templates/import', fd)
			    .then((res) => {
			      if(res.data.saved) {
			      	this.$message.success(this.$t('saved_success'))
			      	this.$router.push(`/${this.url}/${res.data.id}`)
			      }
			    })
			    .catch((error) => {
			      if(error.response.status === 422) {
			          this.errors = error.response.data.errors
			      }
			      this.$message.error(error.response.data.message)
			    })
			    .finally(() => {
			    	this.isUploading = false
			    })
			},
		}
	}
</script>