export default [
	{
		path: '/payment_requests', component: require('@js/views/payment_requests/index.vue'),
		meta: { resource: 'payment_requests' }
	},
	{
		path: '/payment_requests/:id', component: require('@js/views/payment_requests/show.vue'),
		meta: { resource: 'payment_requests'}
	}
]