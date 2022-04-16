import role from './settings/role'
import user from './settings/user'
import invitation from './settings/invitation'

export default [
	{
		path: '/settings', component: require('@js/views/settings/base.vue'),
		children: [
			...role,
			...user,
			...invitation,
			{
				path: '', component: require('@js/views/settings/general.vue'),
				meta: { resource: 'settings/general' }
			},
			{
				path: 'contacts', component: require('@js/views/settings/contacts.vue'),
				meta: { resource: 'settings/custom_fields?type=contacts' }
			},
			{
				path: 'organizations', component: require('@js/views/settings/organizations.vue'),
				meta: { resource: 'settings/organizations' }
			},
			{
				path: 'items', component: require('@js/views/settings/items.vue'),
				meta: { resource: 'settings/items' }
			},
			{
				path: 'leads', component: require('@js/views/settings/leads.vue'),
				meta: { resource: 'settings/leads' }
			},
			{
				path: 'opportunities', component: require('@js/views/settings/opportunities.vue'),
				meta: { resource: 'settings/opportunities' }
			},
			{
				path: 'proposals', component: require('@js/views/settings/proposals.vue'),
				meta: { resource: 'settings/proposals' }
			},
			{
				path: 'contracts', component: require('@js/views/settings/contracts.vue'),
				meta: { resource: 'settings/contracts' }
			},
			{
				path: 'projects', component: require('@js/views/settings/projects.vue'),
				meta: { resource: 'settings/projects' }
			},
			{
				path: 'invoices', component: require('@js/views/settings/invoices.vue'),
				meta: { resource: 'settings/invoices' }
			},
			{
				path: 'payments', component: require('@js/views/settings/payments.vue'),
				meta: { resource: 'settings/payments' }
			},
			{
				path: 'expenses', component: require('@js/views/settings/expenses.vue'),
				meta: { resource: 'settings/expenses' }
			},
			{
				path: 'vendors', component: require('@js/views/settings/vendors.vue'),
				meta: { resource: 'settings/vendors' }
			},
			{
				path: 'email', component: require('@js/views/settings/email.vue'),
				meta: { resource: 'settings/email_base' }
			},
			{
				path: 'web_payment', component: require('@js/views/settings/web_payment.vue'),
				meta: { resource: 'settings/web_payments' }
			},
			{
				path: 'globals', component: require('@js/views/settings/globals.vue'),
				meta: { resource: 'settings/globals' }
			}
		]
	}
]