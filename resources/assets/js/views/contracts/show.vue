<template>
	<div class="content-inner" v-if="show">
		<x-panel padding margin>
			<div slot="title">
				<router-link to="/contracts">{{$t('contracts')}}</router-link> / {{model.number}}
			</div>
			<div slot="extra">
				<router-link :to="`/contracts`" class="btn btn-default btn-sm">
					<small class="icon icon-arrow-left-c"></small>
				</router-link>
				<x-button icon="email" size="sm" @click="toggleModal"></x-button>
				<x-dropdown size="sm" dir="right" icon="more">
					<x-dropdown-menu slot="menu">
						<x-dropdown-item v-for="(stage, i) in model.all_statuses"
							:key="stage.id"
							@click.native="markAs(stage.id)">
							{{$t('mark_as')}}  {{stage.name}}
						</x-dropdown-item>

						<x-dropdown-link divide :to="`/contracts/${model.id}/edit`">
							{{$t('edit')}}
						</x-dropdown-link>
						<x-dropdown-item>
							<a @click.stop="removeDB('contracts', model.id)">{{$t('delete')}}</a>
						</x-dropdown-item>
					</x-dropdown-menu>
				</x-dropdown>
			</div>
			<x-row line>
				<x-group col="8" label="number">
					<p>{{model.number}}</p>
				</x-group>
				<x-group col="8" label="contact">
					<p v-if="model.contact">
						<router-link :to="`/contacts/${model.contact_id}`">
							{{model.contact.name}}
						</router-link>
					</p>
				</x-group>
				<x-group col="8" label="template">
					<p v-if="model.template">
						<router-link :to="`/templates/${model.template_id}`">
							{{model.template.name}}
						</router-link>
					</p>
				</x-group>
			</x-row>
			<x-row line>
				<x-group col="8" label="title">
					<p>{{model.title}}</p>
				</x-group>
				<x-group col="8" label="value">
					<p>{{model.value | formatMoney}}</p>
				</x-group>
				<x-group col="8" label="type">
					<p>{{model.type.name}}</p>
				</x-group>
				<x-group col="8" label="proposal">
					<p v-if="model.proposal">
						<router-link :to="`/proposals/${model.proposal_id}`">
							{{model.proposal.number}}
						</router-link>
					</p>
					<p v-else>-</p>
				</x-group>
			</x-row>
			<x-row line>
				<x-group col="8" label="start_date">
					<p>{{model.start_date | toDate}}</p>
				</x-group>
				<x-group col="8" label="expiry_date">
					<p v-if="model.expiry_date">{{model.expiry_date | toDate}}</p>
					<p v-else>-</p>
				</x-group>
				<x-group col="8" label="auto_renewal">
					<p>{{model.auto_renewal ? $t('yes') : $t('no')}}</p>
				</x-group>
				<x-group col="8" label="no_of_months" v-if="model.auto_renewal">
					<p>{{model.no_of_months}}</p>
				</x-group>
			</x-row>
			<x-row v-if="model.custom_fields_2 && model.custom_fields_2.length > 0" line>
				<custom-field-preview :field="field" :key="field.name"
					v-for="field in model.custom_fields_2" />
			</x-row>
			<x-row line>
				<x-group col="8" label="created_at">
					<pre>{{model.created_at | toDate}}</pre>
				</x-group>
				<x-group col="8" label="status">
					<span :class="`status status-${model.status.color}`">
						<span class="status-text">{{model.status.name}}</span>
					</span>
				</x-group>
			</x-row>
		</x-panel>
		<x-panel>
			<div slot="title">{{$t('preview')}}</div>
			<div slot="extra">
				<a target="_blank" :href="`/api/contracts/${model.id}/preview`" class="btn btn-default btn-sm">
					<small class="icon icon-android-download"></small>
				</a>
			</div>
			<div class="template-preview">
				<object :data="`/api/contracts/${model.id}/preview`" type="application/pdf" width="100%" height="100%">
				</object>
			</div>
		</x-panel>
		<email-modal v-if="showModal" :id="model.id" type="contract" @cancel="toggleModal" @ok="onSaved"></email-modal>

	</div>
</template>
<script>
	import { showable } from '@js/lib/mixins'
	import CustomFieldPreview from '@js/partials/CustomFieldPreview.vue'
	import EmailModal from '@js/partials/EmailModal.vue'
	export default {
		mixins: [ showable ],
		components: { CustomFieldPreview, EmailModal },
		data() {
			return {
				showModal: false,
				show: false,
				model: {
					notes: [],
					contact: {},
					proposal: {},
					status: {},
					all_statuses: [],
					custom_fields_2: []
				},
			}
		},
		methods: {
			onSaved() {
				this.showModal = false
				const id = Math.random().toString(36).substring(7)
				this.$router.push(`?id=${id}`)
			},
			toggleModal() {
				this.showModal = !this.showModal
			},
			markAs(type) {
				this.$bar.start()
				this.$http.post(`/api/mark-as/contracts/${this.model.id}`, {type: type})
					.then((res) => {
						if(res.data.saved) {
							this.$set(this.model, 'status', res.data.status)
						}
					})
					.finally((res) => {
						this.$bar.finish()
					})
			}
		}
	}
</script>