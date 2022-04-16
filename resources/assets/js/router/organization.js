export default [
	{
		path: '/organizations', component: require('@js/views/organizations/index.vue'),
		meta: { resource: 'organizations' }
	},
	{
		path: '/organizations/create', component: require('@js/views/organizations/form.vue'),
		meta: { resource: 'organizations', mode: 'create' }
	},
	{
		path: '/organizations/:id/edit', component: require('@js/views/organizations/form.vue'),
		meta: { resource: 'organizations', mode: 'edit' }
	},
	{
		path: '/organizations/:id', component: require('@js/views/organizations/show.vue'),
		meta: { resource: 'organizations'}
	}
]