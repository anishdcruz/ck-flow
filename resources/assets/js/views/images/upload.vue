<template>
	<x-modal :loading="isUploading" :footer="showFooter" @close="cancel">
		<div slot="title">{{isUploading ? $t('uploading') :$t('upload')}}</div>
		<div class="upload">
			<div v-if="files && files.length > 0">
				<template v-for="(file, index) in files">
					<x-row class="upload-item">
						<x-col span="4">
							<file-preview :file="file"></file-preview>
						</x-col>
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
					<span class="icon icon-images"></span>
				</div>
				<p class="upload-text">{{$t('drag_and_drop')}}</p>
				<div class="upload-browse">
					<x-button size="sm" @click="onBrowse">{{$t('browse')}}</x-button>
					<input v-show="false" ref="input"
        		type="file" @change="handleUpload" multiple>
				</div>
			</div>
		</div>
	</x-modal>
</template>
<script>
	import FilePreview from '@js/components/upload/FilePreview.vue'
	export default {
		components: { FilePreview },
		data() {
			return {
				isUploading: false,
				showFooter: false,
				files: [],
				errors: {}
			}
		},
		methods: {
			cancel() {
				if(this.isUploading) return
				this.$emit('cancel')
			},
			onBrowse() {
			  this.$refs.input.click()
			},
			onDragOver() {},
			onDrop(e) {
			  const files = e.dataTransfer.files
			  for(var i = 0; i< files.length; i++) {
			  	if (files[i].type.match(/image.*/)) {
			  		this.files.push(files[i])
			  	}
			  }

			  this.postUpload()
			},
			handleUpload(e) {
        const files = e.target.files
        for(var i = 0; i< files.length; i++) {
			  	if (files[i].type.match(/image.*/)) {
			  		this.files.push(files[i])
			  	}
			  }
        this.postUpload()
      },
			postUpload() {
			  var fd = new FormData()
			  for(var i = 0; i< this.files.length; i++) {
			    fd.append(`images[${i}]`, this.files[i])
			  }
			  this.save(fd)
			},
			save(fd) {
			  this.isUploading = true
			  this.$http.post('/api/images', fd)
			    .then((res) => {
			      if(res.data.saved) {
			      	this.$message.success(this.$t('saved_success'))
			        this.$emit('saved', res.data)
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