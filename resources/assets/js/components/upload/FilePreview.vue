<template>
	<div class="upload-preview">
		<span class="icon icon-x" v-if="inValid"></span>
		<img :src="preview" v-if="preview">
	</div>
</template>
<script>
	export default {
		props: {
			file: File
		},
		data() {
			return {
				preview: null,
				inValid: false
			}
		},
		mounted() {
			if (!this.file || !this.file.type.match(/image.*/)) {
				this.inValid = true
				return
			}
			const fileReader = new FileReader()

			fileReader.onload = (event) => {
			    this.preview = event.target.result
			}

			fileReader.readAsDataURL(this.file)
		}
	}
</script>