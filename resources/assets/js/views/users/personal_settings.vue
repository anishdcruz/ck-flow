<template>
	<div class="content-inner" v-if="show">
	<x-panel padding margin>
		<div slot="title">{{$t('personal_settings')}}</div>
		<x-row line>
			<x-col span="8">
				<h3>{{$t('application')}}</h3>
			</x-col>
			<x-col span="12">
				<x-form-group :errors="errors.name" v-model="form.name" :label="$t('name')"></x-form-group>
				<x-switch :label="$t('change_password')" v-model="change_password"
              @change="onPassword"
          />
				<x-form-group  v-if="change_password" optional type="password" :errors="errors.old_password" v-model="form.old_password" :label="$t('old_password')"></x-form-group>
				<x-form-group  v-if="change_password" optional type="password" :errors="errors.new_password" v-model="form.new_password" :label="$t('new_password')"></x-form-group>
				<x-form-group  v-if="change_password" optional type="password" :errors="errors.new_password_confirmation" v-model="form.new_password_confirmation" :label="$t('new_password_confirmation')"></x-form-group>
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
				redirect: 'personal_settings',
				change_password: 0,
				form: {
				},
			}
		},
		methods: {
			onPassword(e) {
			    if(this.change_password) {
			        this.$set(this.form, 'old_password', null)
			        this.$set(this.form, 'new_password', null)
			        this.$set(this.form, 'new_password_confirmation', null)
			    } else {
			        this.$delete(this.form, 'old_password')
			        this.$delete(this.form, 'new_password')
			        this.$delete(this.form, 'new_password_confirmation')
			    }
			},
			setData(res) {
				this.$set(this.$data, 'form', res.data.form)
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
						this.$set(window.FLOW.user, 'name', this.form.name)
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