export default [
	{
		path: '/vendors', component: require('@js/views/vendors/index.vue'),
		meta: { resource: 'vendors' }
	},
	{
		path: '/vendors/create', component: require('@js/views/vendors/form.vue'),
		meta: { resource: 'vendors', mode: 'create' }
	},
	{
		path: '/vendors/:id/edit', component: require('@js/views/vendors/form.vue'),
		meta: { resource: 'vendors', mode: 'edit' }
	},
	{
		path: '/vendors/:id', component: require('@js/views/vendors/show.vue'),
		meta: { resource: 'vendors'}
	}
]