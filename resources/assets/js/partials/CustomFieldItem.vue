<template>
	<div class="form-group">
    <label class="form-label"
    	@mouseenter="isHovering = true"
			@mouseleave="isHovering = false">
      <span>{{field.label}}</span>
      <small class="form-optional" v-if="isEdit">
      	<x-width v-model="field.width"></x-width>
      	<span class="form-icon icon icon-edit" @click="toggleFieldModal"></span>
      	<span class="form-icon icon icon-close" @click="removeField"></span>
      </small>
    </label>
    <div v-if="field.type === 'select'">
      <select class="form-input" v-model="field.model">
        <option v-for="option in field.options">{{option}}</option>
      </select>
    </div>
    <div v-if="field.type === 'text'">
      <input type="text" class="form-input" v-model="field.model">
    </div>
    <div v-if="field.type === 'number'">
      <input type="number" class="form-input" v-model="field.model">
    </div>
    <div v-if="field.type === 'date'">
      <input type="date" class="form-input" v-model="field.model">
    </div>
    <div v-if="field.type === 'image'">
      <field-image v-model="field.model"></field-image>
    </div>
    <div v-if="field.type === 'currency'">
      <input type="number" class="form-input" v-model="field.model">
    </div>
    <div v-if="field.type === 'textarea'">
      <textarea class="form-input" v-model="field.model"></textarea>
    </div>
    <field-type-modal v-if="showFieldModal" @cancel="toggleFieldModal"
      :field="field" @update="onUpdate"></field-type-modal>
  </div>
</template>
<script>
  import FieldTypeModal from '@js/partials/CustomFieldModal.vue'
  import FieldImage from '@js/partials/FieldImage.vue'
	export default {
    components: { FieldTypeModal, FieldImage },
		props: {
			field: Object,
			page: String,
			editable: {
				default: true
			}
		},
		data() {
			return {
				isHovering: false,
				showFieldModal: false
			}
		},
		computed: {
			varName() {
				return `${this.page}.${field.name}`
			},
			isEdit() {
				return this.isHovering && this.editable
			}
		},
		methods: {
			onUpdate(e) {
				this.$emit('update', e)
				this.toggleFieldModal();
			},
			toggleFieldModal() {
			  this.showFieldModal = !this.showFieldModal
			},
			removeField() {
				this.$emit('remove-field')
			}
		}
	}
</script>