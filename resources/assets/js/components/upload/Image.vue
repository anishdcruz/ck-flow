<template>
  <div class="upload-image">
  	<div v-if="showPreview" class="upload-image-preview">
  		<div class="upload-image-close" @click.prevent="close">
  			<span class="icon icon-x"></span>
  		</div>
  		<img :src="preview" class="upload-image-img">
  	</div>
  	<div class="upload-image-inner" v-else>
  		<div class="upload-image-content">
  			<span class="upload-image-icon icon icon-image"></span>
        <input type="file" @change="handleChange">
  		</div>
  	</div>
  </div>
</template>
<script>
  export default {
  	name: 'XImageUpload',
    model: {
      prop: 'value',
      event: 'input'
    },
    props: {
      value: [String, Number],
      url: [String]
    },
  	data() {
  		return {
        show: true,
  			uploadedFile: null,
        uploading: false,
        errors: {}
  		}
  	},
    computed: {
      showPreview() {
        return this.show
      },
      preview () {
        return this.value ? this.value : this.uploadedFile
      }
    },
  	methods: {
  		close() {
  			this.uploadedFile = null
        this.show = false
  		},
  		handleChange(e) {
  			this.upload(e.target.files[0])
  		},
  		upload(file) {
				if (!file || !file.type.match(/image.*/)) return
				const fileReader = new FileReader()

				fileReader.onload = (event) => {
				    this.uploadedFile = event.target.result
            this.show = true
            this.save(file)
				}

				fileReader.readAsDataURL(file)
  		},
      save(file) {
        this.uploading = false
        let fd = new FormData()
        fd.append('image_upload', file, file.name)
        this.$http.post(this.url, fd)
          .then((res) => {
            this.uploading = false
            this.$emit('input', res.data.image_path)
            this.$message.success('Successfully upload image!')
          })
          .catch((error) => {
            this.uploading = false
            if(error.response.status === 422) {
                this.errors = error.response.data.errors
            }
            this.$message.error(error.response.data.message)
          })
      }
  	}
  }
</script>