export default [
	{
		path: '/projects', component: require('@js/views/projects/index.vue'),
		meta: { resource: 'projects' }
	},
	{
		path: '/projects/create', component: require('@js/views/projects/form.vue'),
		meta: { resource: 'projects', mode: 'create' }
	},
	{
		path: '/projects/:id/edit', component: require('@js/views/projects/form.vue'),
		meta: { resource: 'projects', mode: 'edit' }
	},
	{
		path: '/projects/:id', component: require('@js/views/projects/show.vue'),
		meta: { resource: 'projects'}
	}
]