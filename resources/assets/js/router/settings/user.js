export default [
	{
		path: 'users', component: require('@js/views/settings/users/index.vue'),
		meta: { resource: 'settings/users' }
	},
	{
		path: 'users/create', component: require('@js/views/settings/users/form.vue'),
		meta: { resource: 'settings/users', mode: 'create' }
	},
	{
		path: 'users/:id/edit', component: require('@js/views/settings/users/form.vue'),
		meta: { resource: 'settings/users', mode: 'edit' }
	},
	{
		path: 'users/:id', component: require('@js/views/settings/users/show.vue'),
		meta: { resource: 'settings/users'}
	}
]