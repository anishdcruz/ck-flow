export default [
	{
		path: '/invoices', component: require('@js/views/invoices/index.vue'),
		meta: { resource: 'invoices' }
	},
	{
		path: '/invoices/create', component: require('@js/views/invoices/form.vue'),
		meta: { resource: 'invoices', mode: 'create' }
	},
	{
		path: '/invoices/:id/edit', component: require('@js/views/invoices/form.vue'),
		meta: { resource: 'invoices', mode: 'edit' }
	},
	{
		path: '/invoices/:id', component: require('@js/views/invoices/show.vue'),
		meta: { resource: 'invoices'}
	}
]