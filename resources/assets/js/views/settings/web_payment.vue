<template>
	<div class="content-inner" v-if="show">
		<x-panel padding margin>
			<div slot="title">{{$t('x_settings', {title: $t('web_payment')})}}</div>
			<x-row line>
				<x-form-group col="8" :errors="errors.active_payment_gateway" :label="$t('active_payment_gateway')">
					<select class="form-input" v-model="form.active_payment_gateway">
						<option value="stripe">{{$t('stripe')}}</option>
						<option value="paypal">{{$t('paypal')}}</option>
						<option value="razorpay">{{$t('razorpay')}}</option>
					</select>
				</x-form-group>
			</x-row>
			<x-row line>
				<x-form-group col="24" :label="$t('payment_notification_email')"
					v-model="form.payment_notification_email" optional
					:errors="errors.payment_notification_email">
				</x-form-group>
				<x-form-group col="24" :label="$t('web_payment_notification_email_subject')"
					:errors="errors.web_payment_notification_email_subject"
					v-model="form.web_payment_notification_email_subject">
					</x-form-group>
				<x-form-group col="24" :errors="errors.web_payment_notification_email_template"
					:label="$t('web_payment_notification_email_template')">
				  <vue-trix v-model="form.web_payment_notification_email_template"></vue-trix>
				</x-form-group>
			</x-row>
			<x-row line>
				<x-form-group col="24" :label="$t('web_payment_email_subject')"
					:errors="errors.web_payment_email_subject"
					v-model="form.web_payment_email_subject">
					</x-form-group>
				<x-form-group col="24" :errors="errors.payment_success_email"
					:label="$t('payment_success_email')">
				  <vue-trix v-model="form.payment_success_email"></vue-trix>
				</x-form-group>
			</x-row>
			<x-row line>
				<x-form-group col="24" :label="$t('payment_request_email_subject')"
					:errors="errors.payment_request_email_subject"
					v-model="form.payment_request_email_subject">
					</x-form-group>
				<x-form-group col="24" :errors="errors.payment_request_email_template"
					:label="$t('payment_request_email_template')">
				  <vue-trix v-model="form.payment_request_email_template"></vue-trix>
				</x-form-group>
			</x-row>
		</x-panel>
		<x-panel padding margin>
			<div slot="title">{{$t('x_settings', {title: $t('stripe')})}}</div>
			<div slot="extra">
				<x-switch v-model="form.enable_stripe"></x-switch>
			</div>
			<div v-if="form.enable_stripe">
				<x-row line>
					<x-form-group col="8" :label="$t('stripe_publishable_key')"
						v-model="form.stripe_publishable_key"
						:errors="errors.stripe_publishable_key">
					</x-form-group>
					<x-form-group col="8" :label="$t('stripe_secret_key')"
						v-model="form.stripe_secret_key"
						:errors="errors.stripe_secret_key">
					</x-form-group>
				</x-row>
				<x-row line>
					<x-form-group col="8" :label="$t('stripe_title')"
						v-model="form.stripe_title"
						:errors="errors.stripe_title">
					</x-form-group>
					<x-form-group col="8" :label="$t('stripe_description')"
						v-model="form.stripe_description"
						:errors="errors.stripe_description">
					</x-form-group>
					<x-form-group col="8" :label="$t('stripe_logo_url')"
						v-model="form.stripe_logo_url"
						:errors="errors.stripe_logo_url">
					</x-form-group>
				</x-row>
				<x-row line>
					<x-form-group col="8" :label="$t('stripe_payment_method_id')" :errors="errors.stripe_payment_method_id">
						<x-tag-input :value="form.stripe_payment_method" resource="payment_methods" column="name" name="name"
						    @input="value => { form.stripe_payment_method_id = value.id; form.stripe_payment_method = value }">
						</x-tag-input>
					</x-form-group>
					<x-form-group col="8" :label="$t('stripe_payment_deposit_id')" :errors="errors.stripe_payment_deposit_id">
						<x-tag-input :value="form.stripe_payment_deposit" resource="payment_deposits" column="name" name="name"
						    @input="value => { form.stripe_payment_deposit_id = value.id; form.stripe_payment_deposit = value }">
						</x-tag-input>
					</x-form-group>
				</x-row>
			</div>
		</x-panel>
		<x-panel padding margin>
			<div slot="title">{{$t('x_settings', {title: $t('razorpay')})}}</div>
			<div slot="extra">
				<x-switch v-model="form.enable_razorpay"></x-switch>
			</div>
			<div v-if="form.enable_razorpay">
				<x-row line>
					<x-form-group col="8" :label="$t('razorpay_api_key')"
						v-model="form.razorpay_api_key"
						:errors="errors.razorpay_api_key">
					</x-form-group>
					<x-form-group col="8" :label="$t('razorpay_secret_key')"
						v-model="form.razorpay_secret_key"
						:errors="errors.razorpay_secret_key">
					</x-form-group>
				</x-row>
				<x-row line>
					<x-form-group col="8" :label="$t('razorpay_title')"
						v-model="form.razorpay_title"
						:errors="errors.razorpay_title">
					</x-form-group>
					<x-form-group col="8" :label="$t('razorpay_description')"
						v-model="form.razorpay_description"
						:errors="errors.razorpay_description">
					</x-form-group>
					<x-form-group col="8" :label="$t('razorpay_logo_url')"
						v-model="form.razorpay_logo_url"
						:errors="errors.razorpay_logo_url">
					</x-form-group>
				</x-row>
				<x-row line>
					<x-form-group col="8" :label="$t('razorpay_payment_method_id')" :errors="errors.razorpay_payment_method_id">
						<x-tag-input :value="form.razorpay_payment_method" resource="payment_methods" column="name" name="name"
						    @input="value => { form.razorpay_payment_method_id = value.id; form.razorpay_payment_method = value }">
						</x-tag-input>
					</x-form-group>
					<x-form-group col="8" :label="$t('razorpay_payment_deposit_id')" :errors="errors.razorpay_payment_deposit_id">
						<x-tag-input :value="form.razorpay_payment_deposit" resource="payment_deposits" column="name" name="name"
						    @input="value => { form.razorpay_payment_deposit_id = value.id; form.razorpay_payment_deposit = value }">
						</x-tag-input>
					</x-form-group>
				</x-row>
			</div>
		</x-panel>
		<x-panel padding margin>
			<div slot="title">{{$t('x_settings', {title: $t('paypal')})}}</div>
			<div slot="extra">
				<x-switch v-model="form.enable_paypal"></x-switch>
			</div>
			<div v-if="form.enable_paypal">
				<x-row line>
					 <x-form-group col="8" :errors="errors.paypal_mode" :label="$t('paypal_mode')">
				  	<select v-model="form.paypal_mode" class="form-input">
				  		<option value="sandbox">{{$t('sandbox')}}</option>
				  		<option value="production">{{$t('production')}}</option>
				  	</select>
				  </x-form-group>
				</x-row>
				<x-row line>
					<x-form-group col="8" :label="$t('paypal_sandbox_client_id')"
						v-model="form.paypal_sandbox_client_id"
						:errors="errors.paypal_sandbox_client_id">
					</x-form-group>
					<x-form-group col="8" :label="$t('paypal_sandbox_secret')"
						v-model="form.paypal_sandbox_secret"
						:errors="errors.paypal_sandbox_secret">
					</x-form-group>
				</x-row>
				<x-row line>
					<x-form-group col="8" :label="$t('paypal_production_client_id')"
						v-model="form.paypal_production_client_id"
						:errors="errors.paypal_production_client_id">
					</x-form-group>
					<x-form-group col="8" :label="$t('paypal_production_secret')"
						v-model="form.paypal_production_secret"
						:errors="errors.paypal_production_secret">
					</x-form-group>
				</x-row>
				<x-row line>
					<x-form-group col="8" :label="$t('paypal_locale')"
						v-model="form.paypal_locale"
						:errors="errors.paypal_locale">
					</x-form-group>
					<x-form-group col="8" :label="$t('paypal_size')"
						v-model="form.paypal_size"
						:errors="errors.paypal_size">
					</x-form-group>
					<x-form-group col="8" :label="$t('paypal_color')"
						v-model="form.paypal_color"
						:errors="errors.paypal_color">
					</x-form-group>
					<x-form-group col="8" :label="$t('paypal_shape')"
						v-model="form.paypal_shape"
						:errors="errors.paypal_shape">
					</x-form-group>
				</x-row>
				<x-row line>
					<x-form-group col="8" :label="$t('paypal_payment_method_id')" :errors="errors.paypal_payment_method_id">
						<x-tag-input :value="form.paypal_payment_method" resource="payment_methods" column="name" name="name"
						    @input="value => { form.paypal_payment_method_id = value.id; form.paypal_payment_method = value }">
						</x-tag-input>
					</x-form-group>
					<x-form-group col="8" :label="$t('paypal_payment_deposit_id')" :errors="errors.paypal_payment_deposit_id">
						<x-tag-input :value="form.paypal_payment_deposit" resource="payment_deposits" column="name" name="name"
						    @input="value => { form.paypal_payment_deposit_id = value.id; form.paypal_payment_deposit = value }">
						</x-tag-input>
					</x-form-group>
				</x-row>
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
				redirect: 'settings/web_payment',
				form: {
				},
			}
		}
	}
</script>