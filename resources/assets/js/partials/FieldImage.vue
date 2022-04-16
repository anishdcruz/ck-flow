<template>
	<div :class="[`${type} form-image`]">
		<div class="form-image-thumb-wrap" v-if="showThumb">
			<span class="thumb-remove icon icon-edit" @click="removeThumb"></span>
			<img class="form-image-thumb" :src="value" v-if="value">
		</div>
		<div class="form-image-inner"
			@dragover.prevent="onDragOver"
      @drop.prevent="onDrop" v-else>
			<span class="thumb-remove icon icon-close" v-if="showForm"
				@click="cancelForm"></span>
			<div class="form-image-ctrl">
				<a @click.stop="toggleModal">Browse Library</a>
			</div>
		</div>
		<input v-show="false" ref="input" type="file" @change="handleUpload">
		<browse-library v-if="showModal" @cancel="toggleModal" @select="handleSelect"></browse-library>
	</div>
</template>
<script>
	import BrowseLibrary from '@js/partials/BrowseLibrary.vue'
	export default {
		components: { BrowseLibrary },
		model: {
		  prop: 'value',
		  event: 'input'
		},
		props: {
		  value: [String],
		  type: {
		  	default: 'form-input'
		  }
		},
		data() {
			return {
				showModal: false,
				showForm: false,
				files: []
			}
		},
		computed: {
			showThumb() {
				if(this.showForm) return false
				return this.value
			}
		},
		methods: {
			cancelForm() {
				this.showForm = false
			},
			removeThumb() {
				this.showForm = true
			},
			toggleModal() {
				this.showModal = !this.showModal
			},
			handleSelect(e) {
				const f = e.target.value
				this.$emit('input', f.filename)
				this.cancelForm()
				this.toggleModal()
			},
			onUploadClick() {
			  this.$refs.input.click()
			},
			handleUpload(e) {
			  const files = e.target.files
			  for(var i = 0; i< files.length; i++) {
			    this.files.push(files[i])
			  }
			  this.showFiles = true
			  this.postUpload()
			},
			postUpload() {
			  var fd = new FormData()
			  for(var i = 0; i< this.files.length; i++) {
			    fd.append(`images[${i}]`, this.files[i])
			  }
			  this.save(fd)
			},
			onDragOver() {

			},
			onDrop(e) {
			  const files = e.dataTransfer.files
			  for(var i = 0; i< files.length; i++) {
			    this.files.push(files[i])
			  }
			  this.showFiles = true
			  this.postUpload()
			},
			save(fd) {
			  this.isUploading = true
			  this.$http.post('/api/images', fd)
			    .then((res) => {
			      if(res.data.saved && res.data.images[0]) {
			      	this.$emit('input', res.data.images[0].filename)
			        this.cancelForm()
			      }
			    })
			    .catch((error) => {
			      this.isUploading = false
			      if(error.response.status === 422) {
			          this.errors = error.response.data.errors
			      }
			      this.$message.error(error.response.data.message)
			    })
			},
		}
	}
</script>