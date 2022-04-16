<template>
	<div class="content-inner" v-if="show">
	<x-panel padding margin>
		<div slot="title">{{$t('x_settings', {title: $t('email')})}}</div>
		<x-row line>
			<x-form-group col="8" :errors="errors.email_from_name" v-model="form.email_from_name" :label="$t('email_from_name')"></x-form-group>
			<x-form-group col="8" :errors="errors.email_from_address" v-model="form.email_from_address" :label="$t('email_from_address')"></x-form-group>
			<x-form-group col="8" :errors="errors.global_bcc" v-model="form.global_bcc" :label="$t('global_bcc')" optional></x-form-group>
		</x-row>
		<x-row line>
			<x-form-group col="24" :label="$t('recurring_export_email_subject')"
				:errors="errors.recurring_export_email_subject"
				v-model="form.recurring_export_email_subject">
				</x-form-group>
			<x-form-group col="24" :errors="errors.recurring_export_email_template" :label="$t('recurring_export_email_template')">
			  <vue-trix v-model="form.recurring_export_email_template"></vue-trix>
			</x-form-group>
		</x-row>
		<x-row line>
			<x-form-group col="24" :label="$t('user_invite_email_subject')"
				:errors="errors.user_invite_email_subject"
				v-model="form.user_invite_email_subject">
				</x-form-group>
			<x-form-group col="24" :errors="errors.user_invite_email_template" :label="$t('user_invite_email_template')">
			  <vue-trix v-model="form.user_invite_email_template"></vue-trix>
			</x-form-group>
		</x-row>
	</x-panel>
	<x-panel margin>
		<div slot="title">{{$t('email_base_html')}}</div>
		<div>
			<x-form-group :errors="errors.email_base_html" :label="$t('email_base_html')">
			  	<codemirror v-model="form.email_base_html" :options="{mode: 'text/html'}"></codemirror>
			</x-form-group>
		</div>
	</x-panel>
	<x-panel margin>
		<div slot="title">{{$t('email_stylesheet')}}</div>
		<div>
			<x-form-group :errors="errors.email_stylesheet" :label="$t('email_stylesheet')">
			  	<codemirror v-model="form.email_stylesheet" :options="{mode: 'text/css'}"></codemirror>
			</x-form-group>
		</div>
	</x-panel>
	<x-panel>
		<div slot="footer" class="flex flex-end">
			<div>
				<x-button @click="save" type="primary" :loading="isSaving">{{$t('save')}}</x-button>
			</div>
		</div>
	</x-panel>
	</div>
</template>
<script>
	import { settings } from '@js/lib/mixins'


	export default {
		mixins: [ settings ],
		data() {
			return {
				redirect: 'settings/email',
				form: {}
			}
		},
		methods: {

		}
	}
</script>