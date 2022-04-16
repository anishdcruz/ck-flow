export default [
	{
		path: '/items', component: require('@js/views/items/index.vue'),
		meta: { resource: 'items' }
	},
	{
		path: '/items/create', component: require('@js/views/items/form.vue'),
		meta: { resource: 'items', mode: 'create' }
	},
	{
		path: '/items/:id/edit', component: require('@js/views/items/form.vue'),
		meta: { resource: 'items', mode: 'edit' }
	},
	{
		path: '/items/:id', component: require('@js/views/items/show.vue'),
		meta: { resource: 'items'}
	}
]