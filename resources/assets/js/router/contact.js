export default [
	{
		path: '/contacts', component: require('@js/views/contacts/index.vue'),
		meta: { resource: 'contacts' }
	},
	{
		path: '/contacts/create', component: require('@js/views/contacts/form.vue'),
		meta: { resource: 'contacts', mode: 'create' }
	},
	{
		path: '/contacts/:id/edit', component: require('@js/views/contacts/form.vue'),
		meta: { resource: 'contacts', mode: 'edit' }
	},
	{
		path: '/contacts/:id', component: require('@js/views/contacts/show.vue'),
		meta: { resource: 'contacts'}
	}
]