<template>
	<div class="content-inner">
		<x-panel margin>
			<div slot="title">{{$t('overview')}}</div>
			<div slot="extra">
				<x-button size="sm" type="primary" v-if="showEdit"
					@click="toggleFieldModal">{{$t('add_card')}}</x-button>
				<x-button size="sm" type="secondary" @click="toggleEdit">{{$t('edit')}}</x-button>
			</div>
		</x-panel>
		<x-row class="metrics">
			<x-col span="8" v-for="metric in metrics" :key="metric.id">
				<value-card v-if="metric.type === 'value'"
					:edit="showEdit"
					:label="metric.card_label"
					:value="metric.values"
					@remove="removeMetric(metric)"></value-card>
				<chart-card v-else :label="metric.card_label"
					:edit="showEdit"
					:values="metric.values"
					:type="metric.chart_type"
					:color="metric.color"
					@remove="removeMetric(metric)"></chart-card>
			</x-col>
		</x-row>
		<metrics-modal v-if="showModal" @cancel="toggleFieldModal"
	  	@add="handleAddCard"></metrics-modal>
	</div>
</template>
<script>
	import { indexable } from '@js/lib/mixins'
	import MetricsModal from '@js/partials/MetricsModal.vue'
	import ChartCard from '@js/partials/ChartCard.vue'
	import ValueCard from '@js/partials/ValueCard.vue'
	export default {
		mixins: [ indexable ],
		components: { MetricsModal, ChartCard, ValueCard },
		data() {
			return {
				showEdit: false,
				showModal: false,
				show: false,
				metrics: []
			}
		},
		methods: {
			removeMetric(item) {
				const r = confirm(this.$t('are_you_sure'))
				if(r != true) { return }

				this.$bar.start()
				this.$http.delete(`/api/user_metrics/${item.id}`)
				    .then((res) => {
				        if(res.data.deleted) {
                  	const id = Math.random().toString(36).substring(7)
                  	this.showEdit = false
										this.$router.push(`/?id=${id}`)
				            this.$message.success(this.$t('success_delete'))
				        }
				    })
				    .catch((error) => {
				    	this.$bar.finish()
				      if(error && error.response.status === 422) {
				        this.$message.error(error.response.data.message)
				      }
				    })
			},
			handleAddCard() {

			},
			toggleEdit() {
				this.showEdit = !this.showEdit
			},
			toggleFieldModal() {
				this.showModal = !this.showModal
			},
			setData(res) {
				this.$set(this.$data, 'metrics', res.data.metrics)
				this.$bar.finish()
			}
		}
	}
</script>