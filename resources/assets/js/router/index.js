import Vue from 'vue'
import VueRouter from 'vue-router'

Vue.use(VueRouter)

import contact from './contact'
import organization from './organization'
import item from './item'
import lead from './lead'
import opportunity from './opportunity'
import image from './image'
import template from './template'
import proposal from './proposal'
import contract from './contract'
import project from './project'
import invoice from './invoice'
import payment from './payment'
import expense from './expense'
import vendor from './vendor'
import paymentRequest from './payment_request'
import recurringExport from './recurring_export'

import settings from './settings'
// import recurring from './recurring'

const router = new VueRouter({
	mode: 'history',
	scrollBehavior (to, from, savedPosition) {
	    if (savedPosition) {
	        return savedPosition
	    } else {
	        return { x: 0, y: 0 }
	    }
	},
	routes: [
		{path: '/', component: require('@js/views/overview/index.vue'),meta: { resource: 'overview'}},
		{path: '/personal_settings', component: require('@js/views/users/personal_settings.vue'),meta: { resource: 'personal_settings'}},
		...contact,
		...organization,
		...item,
		...lead,
		...opportunity,
		...image,
		...template,
		...proposal,
		...contract,
		...project,
		...invoice,
		...payment,
		...expense,
		...vendor,
		...paymentRequest,
		...settings,
		...recurringExport,
		// ...recurring
	]
})

export default router

