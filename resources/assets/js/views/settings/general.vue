<template>
	<div class="content-inner" v-if="show">
	<x-panel padding margin>
		<div slot="title">{{$t('x_settings', {title: $t('general')})}}</div>
		<x-row line>
			<x-col span="8">
				<h3>{{$t('application')}}</h3>
			</x-col>
			<x-col span="12">
				<x-form-group :errors="errors.application_name" v-model="form.application_name" :label="$t('application_name')"></x-form-group>
				<x-form-group :errors="errors.application_date_format" :label="$t('application_date_format')">
					<select class="form-input" v-model="form.application_date_format">
						<option>d-m-Y</option>
						<option>Y-m-d</option>
						<option>d-M-Y</option>
						<option>Y-M-d</option>
						<option>d/m/Y</option>
						<option>Y/m/d</option>
					</select>
				</x-form-group>
				<x-form-group :label="$t('application_timezone')" :errors="errors.application_timezone">
					<select class="form-input" v-model="form.application_timezone">
						<option v-for="(value, key) in options.timezones"
							:value="key">
							{{value}}
						</option>
					</select>
				</x-form-group>
			</x-col>
		</x-row>
		<x-row line>
			<x-col span="8">
				<h3>{{$t('currency')}}</h3>
			</x-col>
			<x-col span="12">
				<x-form-group :errors="errors.currency_code" v-model="form.currency_code" :label="$t('currency_code')"></x-form-group>
				<x-form-group :errors="errors.currency_precision" v-model="form.currency_precision" :label="$t('currency_precision')"></x-form-group>
				<x-form-group :errors="errors.decimal_separator" v-model="form.decimal_separator" :label="$t('decimal_separator')"></x-form-group>
				<x-form-group :errors="errors.thousands_separator" v-model="form.thousands_separator" :label="$t('thousands_separator')"></x-form-group>
				<x-form-group :label="$t('placement')" :errors="errors.placement">
					<select class="form-input" v-model="form.placement">
						<option value="before">{{$t('before')}}</option>
						<option value="after">{{$t('after')}}</option>
					</select>
				</x-form-group>
			</x-col>
		</x-row>
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
				redirect: 'settings',
				form: {
				},
				options: {
					timezones: []
				}
			}
		},
		methods: {
			setData(res) {
				this.$set(this.$data, 'form', res.data.form)
				this.$set(this.$data, 'options', res.data.options)
				this.$bar.finish()
				this.show = true
			},
			save() {
				this.isSaving = true
				this.errors = {}

				const { url, method } = this.getForm()

				this.$http[method](url, this.form)
					.then((res) => {
						const id = Math.random().toString(36).substring(7)
						this.$set(window.FLOW.currency, 'code', this.form.currency_code)
						this.$set(window.FLOW.currency, 'precision', this.form.currency_precision)
						this.$set(window.FLOW.currency, 'decimal_separator', this.form.decimal_separator)
						this.$set(window.FLOW.currency, 'thousands_separator', this.form.thousands_separator)
						this.$set(window.FLOW.currency, 'placement', this.form.placement)

						this.$router.push(`/${this.redirect}?id=${id}`)
						this.$message.success(this.$t('saved_success'))
					})
					.catch(this.catch)
					.finally(() => {
						this.isSaving = false
					})
			},
		}
	}
</script>