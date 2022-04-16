<template>
	<div class="template">
		<div class="template-sidebar">
			<div class="template-sidebar-inner">
				<ul class="template-list">
	        <template v-for="link in links">
	        	<li v-if="link.break" class="template-break"></li>
		        <li>
		        	<a @click.stop="openTab(link.to)"
		        		:class="['template-link', activeTab === link.to ? 'template-active' : '']">
		        		<i :class="`template-icon icon icon-${link.icon}`"></i>
		        		<span class="template-text">
	                {{link.title}}
	              </span>
	              <x-tag v-if="hasError(link.to)" type="error">{{$t('errors')}}</x-tag>
		        	</a>
		        </li>
	        </template>
	        <li class="template-break"></li>
	        <li>
          	<span class="template-control">
          		<strong>{{$t('pages')}}</strong>
          		<x-button type="success" size="sm" @click="handleAddPage">{{$t('add')}}</x-button>
          	</span>
          </li>
          <draggable v-model="form.pages"  @start="drag=true" @end="drag=false">
		        <template v-for="(page, index) in form.pages">
			        <li>
			        	<a @click.stop="openPage(page, index)"
			        		:class="['template-link', activeTab === 'pages' && currentPageIndex === index ? 'template-active' : '']">
			        		<i class="template-icon icon icon-drag"></i>
			        		<span class="template-text">
		                {{page.title}}
		              </span>
		              <template v-if="page.index = index"></template>
		              <x-tag v-if="hasError(`pages.${index}`)" type="error">{{$t('errors')}}</x-tag>
			        	</a>
			        </li>
		        </template>
	        </draggable>
	      </ul>
			</div>
		</div>
		<div class="template-block">
			<div class="template-block-header">
				<div class="template-title">
					{{$t(mode)}} {{$t('template')}} / <small>({{form.name}})</small>
				</div>
				<div v-if="isSaving">
					<x-loading></x-loading>
				</div>
				<div v-else>
					<router-link v-if="mode !== 'create'" :to="`/templates/${form.id}`" class="btn btn-default btn-sm">
						<small class="icon icon-arrow-left-c"></small>
					</router-link>
					<a v-if="mode !== 'create'" :href="`/api/templates/${form.id}/preview`" target="_blank"
						class="btn btn-default btn-sm" size="sm">{{$t('preview')}}</a>
					<x-button size="sm" type="primary" @click="saveTemplate">{{$t('save')}}</x-button>
				</div>
			</div>
			<div class="template-content" ref="content">
				<x-simple-tab :active="activeTab" :tabs="availableTabs">
					<div slot="settings">
						<x-panel padding>
							<div slot="title">{{$t('settings')}}</div>
							<x-row line>
								<x-form-group col="24" v-model="form.name"
									  :errors="errors.name" :label="$t('name')"></x-form-group>
								<x-form-group col="8" :errors="errors.type_id" :label="$t('type')">
								  	<select v-model="form.type_id" class="form-input">
								  		<option v-for="o in opt.types" :value="o.name">{{o.value}}</option>
								  	</select>
							  </x-form-group>
							  <x-form-group col="8" :errors="errors.page_size" :label="$t('page_size')">
							  	<select v-model="form.page_size" class="form-input">
							  		<option v-for="o in opt.sizes" :value="o.name">{{o.value}}</option>
							  	</select>
							  </x-form-group>
							  <x-form-group col="8" :errors="errors.orientation" :label="$t('orientation')">
							  	<select v-model="form.orientation" class="form-input">
							  		<option v-for="o in opt.orientations" :value="o.name">{{o.value}}</option>
							  	</select>
							  </x-form-group>
							</x-row>
							<x-row line>
									<x-form-group col="8" v-model="form.header_height"
									  :errors="errors.header_height" :label="$t('header_height')"></x-form-group>
									<x-form-group col="8" v-model="form.footer_height"
									  :errors="errors.footer_height" :label="$t('footer_height')"></x-form-group>
								</x-row>
						</x-panel>
					</div>
					<div slot="stylesheet">
						<x-panel>
							<div slot="title">{{$t('stylesheet')}}</div>
							<div>
								<x-form-group :errors="errors.stylesheet" :label="$t('stylesheet')">
								  	<codemirror v-model="form.stylesheet" :options="{mode: 'text/css'}"></codemirror>
								</x-form-group>
							</div>
						</x-panel>
					</div>
					<div slot="header">
						<x-panel>
							<div slot="title">
								{{$t('header')}}
							</div>
							<div slot="extra">
								<x-button @click="toggleTempVar" size="sm">{{$t('variables')}}</x-button>
							</div>
							<div>
								<x-form-group :errors="errors.header_html" :label="$t('header_html')">
								  	<codemirror v-model="form.header_html" :options="{mode: 'text/html'}"></codemirror>
								</x-form-group>
							</div>
						</x-panel>
						<div class="panel-gap">
							<div class="panel-gap-title">{{$t('content_fields')}}</div>
							<div class="panel-gap-extra">
								<x-button size="sm"   @click="addSection(form.header_content_fields)">{{$t('add_section')}}</x-button>
							</div>
						</div>
						<div v-for="(section, index) in form.header_content_fields">
							<field-section page="header" :section="section"
								@remove="handleSectionRemove(form.header_content_fields, index)"/>
						</div>
					</div>
					<div slot="footer">
						<x-panel>
							<div slot="title">
								{{$t('footer')}}
							</div>
							<div slot="extra">
								<x-button @click="toggleTempVar" size="sm">{{$t('variables')}}</x-button>
							</div>
							<div>
								<x-form-group :errors="errors.footer_html" :label="$t('footer_html')">
								  	<codemirror v-model="form.footer_html" :options="{mode: 'text/html'}"></codemirror>
								</x-form-group>
							</div>
						</x-panel>
						<div class="panel-gap">
							<div class="panel-gap-title">{{$t('content_fields')}}</div>
							<div class="panel-gap-extra">
								<x-button size="sm"   @click="addSection(form.footer_content_fields)">{{$t('add_section')}}</x-button>
							</div>
						</div>
						<div v-for="(section, index) in form.footer_content_fields">
							<field-section page="footer" :section="section"
								@remove="handleSectionRemove(form.footer_content_fields, index)"/>
						</div>
					</div>
					<div slot="pages" v-if="form.pages[currentPageIndex]">
						<x-panel margin>
							<div style="width: 250px;" slot="title" v-if="isEdit">
								<input type="text" class="form-input"
									:value="form.pages[currentPageIndex].title"
									@input="handleTitle">
							</div>
							<div slot="title" v-else>
								<span>{{form.pages[currentPageIndex].title}}</span>
								<small>({{form.pages[currentPageIndex].name}})</small>
							</div>
							<div slot="extra" v-if="!isEdit">
								<x-button size="sm" icon="edit"
									@click="toggleEdit"></x-button>
								<x-button size="sm"
									type="error" icon="trash-a"
									@click="handleRemove"></x-button>
							</div>
							<div slot="extra" v-else>
								<x-button size="sm" type="success" @click="update">{{$t('ok')}}</x-button>
							</div>
							<div>
								<x-row>
									<x-col span="6">
										<div class="form-group">
											<label class="form-label">
					              {{$t('orientation')}}
					              <small v-if="isEdit" class="form-optional">({{$t('optional')}})</small>
					            </label>
					            <select class="form-input" v-if="isEdit"
					            	v-model="form.pages[currentPageIndex].orientation">
					            	<option value="L">Landscape</option>
					            	<option value="P">Portrait</option>
					            </select>
					            <p v-else>{{form.pages[currentPageIndex].orientation ==='L' ? 'Landscape' : 'Portrait'}}</p>
										</div>
										<div class="form-group">
											<label class="form-label">{{$t('show_header_and_footer')}}</label>
											<x-switch v-if="isEdit" v-model="form.pages[currentPageIndex].header_and_footer"></x-switch>
											<p v-else>{{form.pages[currentPageIndex].header_and_footer === 1 ? 'Enabled' : 'Disabled'}}</p>
										</div>
									</x-col>
									<x-col span="12">
										<div class="form-group">
											<label class="form-label">{{$t('instruction')}}</label>
											<textarea v-if="isEdit" class="form-input" v-model="form.pages[currentPageIndex].instruction"></textarea>
											<pre v-else>{{form.pages[currentPageIndex].instruction}}</pre>
										</div>
									</x-col>
								</x-row>
							</div>
						</x-panel>
						<x-panel>
							<div slot="title">{{$t('page_html')}}</div>
							<div slot="extra">
								<x-button @click="toggleTempVar" size="sm">{{$t('variables')}}</x-button>
							</div>
							<div>
								<codemirror v-model="form.pages[currentPageIndex].page_html" :options="{mode: 'text/html'}"></codemirror>
								<div v-if="errors && errors[`pages.${currentPageIndex}.page_html`]">
									<small class="error-input">{{errors[`pages.${currentPageIndex}.page_html`][0]}}</small>
								</div>
							</div>
						</x-panel>
						<div class="panel-gap">
							<div class="panel-gap-title">{{$t('content_fields')}}</div>
							<div class="panel-gap-extra">
								<x-button size="sm" @click="addSection(form.pages[currentPageIndex].content_fields)">{{$t('add_section')}}</x-button>
							</div>
						</div>
						<div v-for="(section, index) in form.pages[currentPageIndex].content_fields">
							<field-section :page="`${form.pages[currentPageIndex].name}.${$t('cf')}`"
								:section="section"
								@remove="handleSectionRemove(form.pages[currentPageIndex].content_fields, index)" />
						</div>
						<div class="panel-gap">
							<div class="panel-gap-title">{{$t('user_fields')}}</div>
							<div class="panel-gap-extra">
								<x-button size="sm" @click="addSection(form.pages[currentPageIndex].user_fields)">{{$t('add_section')}}</x-button>
							</div>
						</div>
						<div v-for="(section, index) in form.pages[currentPageIndex].user_fields">
							<field-section :page="`${form.pages[currentPageIndex].name}.${$t('uf')}`"
								:section="section"
								@remove="handleSectionRemove(form.pages[currentPageIndex].user_fields, index)" />
						</div>
					</div>
				</x-simple-tab>
			</div>
		</div>
		<template-variables-modal v-if="showTempVar" @cancel="toggleTempVar"
			:type="form.type_id" :form="form" :index="currentPageIndex"></template-variables-modal>
	</div>
</template>
<script>
	import {snakeCase} from 'lodash'
	import { formable } from '@js/lib/mixins'
	import FieldSection from '@js/partials/FieldSection.vue'
	import shared from '@js/shared'
	import TemplateVariablesModal from '@js/partials/TemplateVariablesModal.vue'
	import Draggable from 'vuedraggable'

	export default {
		mixins: [ formable ],
		components: { FieldSection, TemplateVariablesModal, Draggable },
		data() {
			return {
				showTempVar: false,
				shared: shared,
				availableTabs: ['settings', 'stylesheet', 'header', 'footer', 'pages'],
				activeTab: 'settings',
				redirect: 'templates',
				currentPageIndex: null,
				doEdit: false,
				form: {
					pages: [],
					header_content_fields: [],
					footer_content_fields: []
				},
				links: [
					{title: this.$t('settings'), to: 'settings', icon: 'android-settings'},
					{title: this.$t('stylesheet'), to: 'stylesheet', icon: 'social-css3-outline'},
					{title: this.$t('header'), to: 'header', icon: 'document', break: true},
					{title: this.$t('footer'), to: 'footer', icon: 'document'}
				],
				opt: {
					types: [
						{name: 1, value: this.$t('proposal')},
						{name: 2, value: this.$t('contract')},
						{name: 3, value: this.$t('invoice')},
						{name: 4, value: this.$t('payment')},
						// {name: 5, value: this.$t('expense')}
					],
					sizes: [
						{name: 'letter', value: this.$t('letter')},
						{name: 'legal', value: this.$t('legal')},
						{name: 'ledger', value: this.$t('ledger')},
						{name: 'A0', value: this.$t('A0')},
						{name: 'A1', value: this.$t('A1')},
						{name: 'A2', value: this.$t('A2')},
						{name: 'A3', value: this.$t('A3')},
						{name: 'A4', value: this.$t('A4')}
					],
					orientations: [
						{name: 'L', value: this.$t('landscape')},
						{name: 'P', value: this.$t('portrait')}
					]
				}
			}
		},
		computed: {
			hasError() {
				return (key) => {
					if(Object.keys(this.errors).some(function(k){ return ~k.indexOf(key) })){
					   return true
					} else {
						return false
					}
				}
			},
			isEdit() {
				if(this.form.pages[this.currentPageIndex] && this.form.pages[this.currentPageIndex].edit) {
					return this.form.pages[this.currentPageIndex].edit
				} else {
					return this.doEdit
				}
			}
		},
		methods: {
			toggleTempVar() {
				this.showTempVar = !this.showTempVar
			},
			setData(res) {
		    this.$set(this.$data, 'form', res.data.form)
		    this.$set(this.shared, 'itemVariables', res.data.item_variables)
		    this.$set(this.shared, 'template_variables', res.data.template_variables)
		    this.show = true
		    this.$bar.finish()
			},
			toggleEdit() {
				this.doEdit = !this.doEdit
			},
			update() {
				this.doEdit = false
				if(this.form.pages[this.currentPageIndex].edit) {
					this.$delete(this.form.pages[this.currentPageIndex], 'edit')
				}
			},
			handleTitle(e) {
				const v = e.target.value
				this.form.pages[this.currentPageIndex].title = v
				this.form.pages[this.currentPageIndex].name = snakeCase(v)
			},
			handleRemove(e) {
				const r = confirm(this.$t('are_you_sure'))
        if(r != true) { return }

        const dIndex = this.currentPageIndex

      	// last page
      	if(dIndex === this.form.pages.length - 1) {
      		this.currentPageIndex = dIndex - 1
      	}

      	// only one page
      	if(this.form.pages.length === 1) {
      		this.openTab('settings')
      		this.currentPageIndex = 0
      	}

      	this.form.pages.splice(dIndex, 1)
  		},
  		handleAddPage() {
				const nextIndex = this.form.pages.length
				const t = this.$t('new_page')
  			const newPage = {
  				edit: true,
  				name: `${snakeCase(t)}${nextIndex}`,
  				title: `${t} ${nextIndex}`,
  				index: nextIndex,
  				orientation: this.form.orientation,
  				page_html: '',
  				header_and_footer: 1,
  				content_fields: [
  					{title: 'Default', name: 'default', fields: []}
  				],
  				user_fields: [
  					{title: 'Default', name: 'default', fields: []}
  				]
  			}



  			this.form.pages.push(newPage)
  			this.openPage(this.form.pages[nextIndex], nextIndex)
  		},
  		handleSectionRemove(target, index) {
				target.splice(index, 1)
			},
			openTab(name) {
				this.activeTab = name
				this.$nextTick(() => {
					this.$refs.content.scrollTop = 1
				})
			},
			addSection(target) {
				const nextIndex = target.length
				const title = `${this.$t('new_section')} ${nextIndex}`
				const section = {
					name: snakeCase(title),
					title: title,
					fields: [],
					edit: true
				}

				target.push(section)
				this.$nextTick(() => {
					const h2 = this.$refs.content.scrollHeight
					this.$refs.content.scrollTop = h2
				})
			},
			removeSection(target, index) {
				target.splice(index, 1)
			},
			openPage(page, index) {
				this.currentPageIndex = index
				// this.currentPage = page
				this.openTab('pages')
			},
			saveTemplate(){
				this.isSaving = true
				this.errors = {}

				const { url, method } = this.getForm()

				this.$http[method](url, this.form)
					.then((res) => {
						if(method == 'post') {
							this.$router.push(`/${this.redirect}/${res.data.id}/edit`)
						}
						this.$message.success(this.$t('saved_success'))
					})
					.catch((error) => {
						if(error.response.status === 422) {
							this.openTab('settings')
						  this.errors = error.response.data.errors
						}
						this.$message.error(error.response.data.message)
					})
					.finally(() => {
						this.isSaving = false
					})
			}
		}
	}
</script>