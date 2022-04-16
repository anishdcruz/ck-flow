export default [
	{
		path: '/templates', component: require('@js/views/templates/index.vue'),
		meta: { resource: 'templates' }
	},
	{
		path: '/templates/create', component: require('@js/views/templates/form.vue'),
		meta: { resource: 'templates', mode: 'create' }
	},
	{
		path: '/templates/:id/edit', component: require('@js/views/templates/form.vue'),
		meta: { resource: 'templates', mode: 'edit' }
	},
	{
		path: '/templates/:id', component: require('@js/views/templates/show.vue'),
		meta: { resource: 'templates'}
	}
]