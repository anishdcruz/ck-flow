<template>
	<x-form padding :saving="isSaving" @save="save" @cancel="cancel" v-if="show">
		<div slot="title">{{$t(mode)}} {{$t('user')}}</div>
		<x-row line>
			<x-form-group col="8" v-model="form.name" :label="$t('name')" :errors="errors.name"></x-form-group>
			<x-form-group col="8" v-model="form.email" :label="$t('email')" :errors="errors.email"></x-form-group>
			<x-form-group col="8" :label="$t('role')" :errors="errors.role_id">
				<x-tag-input :value="form.role" resource="roles" column="name" name="name"
				    @input="value => { form.role_id = value.id; form.role = value }">
				</x-tag-input>
			</x-form-group>
		</x-row>
		<x-row line>
			<x-form-group type="password" col="8" v-model="form.password" :label="$t('password')" :errors="errors.password" optional></x-form-group>
		</x-row>
	</x-form>
</template>
<script>
	import { formable } from '@js/lib/mixins'
	export default {
		mixins: [ formable ],
		data() {
			return {
				redirect: 'settings/users',
				form: {
					password: null
				}
			}
		}
	}
</script>