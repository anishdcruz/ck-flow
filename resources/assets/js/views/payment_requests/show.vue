<template>
	<div class="content-inner" v-if="show">
		<x-panel padding>
			<div slot="title">
				<router-link to="/payment_requests">{{$t('payment_request')}}</router-link> / {{model.number}}
			</div>
			<div slot="extra">
				<router-link :to="`/payment_requests`" class="btn btn-default btn-sm">
					<small class="icon icon-arrow-left-c"></small>
				</router-link>
				<x-button size="sm" type="error" icon="trash-b" @click="removeDB('payment_requests', model.id)"></x-button>

			</div>
				<x-row line>
					<x-group col="8" label="number">
						<p>{{model.number}}</p>
					</x-group>
					<x-group col="8" label="contact">
						<p v-if="model.contact">
							<router-link :to="`/contacts/${model.contact_id}`">
								{{model.contact.name}}
							</router-link>
						</p>
					</x-group>
					<x-group col="8" label="invoice">
						<p v-if="model.invoice">
							<router-link :to="`/invoices/${model.invoice_id}`">
								{{model.invoice.number}}
							</router-link>
						</p>
					</x-group>
			</x-row>
			<x-row line>
				<x-group col="8" label="email">
					<pre>{{model.email}}</pre>
				</x-group>
				<x-group col="8" label="uuid">
					<pre>{{model.uuid}}</pre>
				</x-group>
			</x-row>
			<x-row line>
				<x-group col="8" label="created_at">
					<pre>{{model.created_at | toDate}}</pre>
				</x-group>
				<x-group col="8" label="expiry_at">
					<pre>{{model.expiry_at | toDate}}</pre>
				</x-group>
				<x-group col="8" label="payment_received_at">
					<p>
						<span v-if="model.payment_received_at">{{model.payment_received_at | toDate}}</span>
						<span v-else>-</span>
					</p>
				</x-group>
			</x-row>
		</x-panel>
	</div>
</template>
<script>
	import { showable } from '@js/lib/mixins'
	export default {
		mixins: [ showable ],
		data() {
			return {
				show: false,
				model: {
					invoice: {},
					contact: {}
				}
			}
		}
	}
</script>