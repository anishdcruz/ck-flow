<template>
	<div>
			<x-panel margin>
				<div slot="title" v-if="isEdit">
					<input type="text" class="form-input" placeholder="New section title"
						:value="section.title" ref="input"
						@input="handleTitle">
				</div>
				<div slot="title" v-else>
					<span>{{section.title}}</span>
					<small>({{section.name}})</small>
				</div>

				<div slot="extra" v-if="!isEdit">
					<x-button size="sm" type="success" @click="toggleFieldModal">{{$t('add_field')}}</x-button>
					<x-button v-if="section.name !=='default'" size="sm" icon="edit"
						@click="toggleEdit"></x-button>
					<x-button v-if="section.name !=='default'" size="sm"
						type="error" icon="close"
						@click="handleRemove"></x-button>
				</div>
				<div slot="extra" v-else>
					<x-button size="sm" type="success" @click="update">{{$t('ok')}}</x-button>
				</div>
				<div class="fields" v-if="section.fields.length > 0">
		      <div :class="[`field field-${field.width}`]" v-for="(field, fIndex) in section.fields">
		      	<field-item :page="page" :field="field"
		      	  @remove-field="handleRemoveField(fIndex, section.fields)"
		      	  @update="handleUpdateField(fIndex, section.fields, $event)"></field-item>
		      </div>
				</div>
				<div v-else>
					<small class="panel-empty">{{$t('no_fields_added')}}</small>
				</div>
			</x-panel>
			<field-type-modal v-if="showFieldModal" @cancel="toggleFieldModal"
      @add="handleAddField"></field-type-modal>
	</div>
</template>
<script>
	import { snakeCase } from 'lodash'
	import FieldItem from '@js/partials/FieldItem.vue'
	import FieldTypeModal from '@js/partials/FieldTypeModal.vue'
	export default {
		components: { FieldItem, FieldTypeModal },
		props: {
			section: Object,
	    page: String
		},
	  	data() {
	  		return {
	        showFieldModal: false,
	  			doEdit: false
	  		}
	  	},
	  	mounted() {
	  		if(this.section && this.section.edit) {
	  			this.$nextTick(() => {
						this.$refs.input.focus()
					})
	  		}
	  	},
	  	computed: {
	  		isEdit() {
	  			if(this.section && this.section.edit) {
	  				return this.section.edit
	  			} else {
	  				return this.doEdit
	  			}
	  		}
	  	},
	  	methods: {
	      handleUpdateField(i, fields, e) {
	         const f = e.target.value
	         this.$set(this.section.fields, i, f)
	      },
	      handleAddField(e) {
	        const f = e.target.value
	        this.section.fields.push(f)
	        this.toggleFieldModal();
	      },
	      toggleFieldModal() {
	        this.showFieldModal = !this.showFieldModal
	      },
	  		toggleEdit() {
	  			this.doEdit = !this.doEdit
	  		},
	  		update() {
	  			this.doEdit = false
	  			if(this.section.edit) {
	  				this.$delete(this.section, 'edit')
	  			}
	  		},

	  		handleTitle(e) {
	  			const v = e.target.value
	  			this.section.title = v
	  			this.section.name = snakeCase(v)
	  		},
	  		handleRemove(e) {
					const r = confirm(this.$t('are_you_sure'))
	        if(r != true) { return }
	  			this.$emit('remove')
	  		},
	      handleRemoveField(index, fields) {
	        const r = confirm(this.$t('are_you_sure'))
	        if(r != true) { return }
	        fields.splice(index, 1)
	      }
	  	}
	}
</script>