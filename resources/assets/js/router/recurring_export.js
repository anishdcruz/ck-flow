export default [
	{
		path: '/recurring_exports', component: require('@js/views/recurring_exports/index.vue'),
		meta: { resource: 'recurring_exports' }
	},
	{
		path: '/recurring_exports/:id', component: require('@js/views/recurring_exports/show.vue'),
		meta: { resource: 'recurring_exports'}
	}
]