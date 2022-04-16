<template>
	<div class="content-inner" v-if="show">
		<x-panel padding>
			<div slot="title">
				<router-link to="/recurring_exports">{{$t('recurring_export')}}</router-link> / {{model.name}}
			</div>
			<div slot="extra">
				<router-link :to="`/recurring_exports`" class="btn btn-default btn-sm">
					<small class="icon icon-arrow-left-c"></small>
				</router-link>
				<x-button size="sm" type="error" icon="trash-b" @click="removeDB('recurring_exports', model.id)"></x-button>
			</div>
				<x-row line>
					<x-group col="8" label="name">
						<p>{{model.name}}</p>
					</x-group>
					<x-group col="8" label="email_to">
					<pre>{{model.email_to}}</pre>
				</x-group>
			</x-row>
			<x-row line>
				<x-group col="8" label="frequency">
					<pre>{{$t(model.frequency)}}</pre>
				</x-group>
				<x-group col="8" label="send_on">
					<pre>{{getSendOn(model)}}</pre>
				</x-group>
				<x-group col="8" label="send_at">
					<pre>{{getTime(model.send_at)}}</pre>
				</x-group>
			</x-row>
			<x-row line>
				<x-group col="8" label="created_at">
					<pre>{{model.created_at | toDate}}</pre>
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
				}
			}
		},
		methods: {
			getSendOn(item) {
			    if(item.frequency === 'weekly') {
			        let weeks = {
			            '1': this.$t('monday'),
			            '2': this.$t('tuesday'),
			            '3': this.$t('wednesday'),
			            '4': this.$t('thursday'),
			            '5': this.$t('friday'),
			            '6': this.$t('saturday'),
			            '7': this.$t('sunday'),
			        }

			        return weeks[item.send_on]
			    } else if(item.frequency === 'monthly') {
			        let list = {
			            '1': '1st',
			            '2': '2nd',
			            '3': '3rd',
			            '4': '4th',
			            '5': '5th',
			            '6': '6th',
			            '7': '7th',
			            '8': '8th',
			            '9': '9th',
			            '10': '10th',
			            '11': '11th',
			            '12': '12th',
			            '13': '13th',
			            '14': '14th',
			            '15': '15th',
			            '16': '16th',
			            '17': '17th',
			            '18': '18th',
			            '19': '19th',
			            '20': '20th',
			            '21': '21st',
			            '22': '22nd',
			            '23': '23rd',
			            '24': '24th',
			            '25': '25th',
			            '26': '26th',
			            '27': '27th',
			            '28': '28th'
			        }
			        return list[item.send_on]
			    } else {
			        return '-'
			    }
			},
			getTime(time) {
			    let times = {

			        '1:00': '1:00 AM',
			        '1:30': '1:30 AM',
			        '2:00': '2:00 AM',
			        '2:30': '2:30 AM',
			        '3:00': '3:00 AM',
			        '3:30': '3:30 AM',
			        '4:00': '4:00 AM',
			        '4:30': '4:30 AM',
			        '5:00': '5:00 AM',
			        '5:30': '5:30 AM',
			        '6:00': '6:00 AM',
			        '6:30': '6:30 AM',
			        '7:00': '7:00 AM',
			        '7:30': '7:30 AM',
			        '8:00': '8:00 AM',
			        '8:30': '8:30 AM',
			        '9:00': '9:00 AM',
			        '9:30': '9:30 AM',
			        '10:00': '10:00 AM',
			        '10:30': '10:30 AM',
			        '11:00': '11:00 AM',
			        '11:30': '11:30 AM',
			        '12:00': '12:00 PM',
			        '12:30': '12:30 PM',
			        '13:00': '1:00 PM',
			        '13:30': '1:30 PM',
			        '14:00': '2:00 PM',
			        '14:30': '2:30 PM',
			        '15:00': '3:00 PM',
			        '15:30': '3:30 PM',
			        '16:00': '4:00 PM',
			        '16:30': '4:30 PM',
			        '17:00': '5:00 PM',
			        '17:30': '5:30 PM',
			        '18:00': '6:00 PM',
			        '18:30': '6:30 PM',
			        '19:00': '7:00 PM',
			        '19:30': '7:30 PM',
			        '20:00': '8:00 PM',
			        '20:30': '8:30 PM',
			        '21:00': '9:00 PM',
			        '21:30': '9:30 PM',
			        '22:00': '10:00 PM',
			        '22:30': '10:30 PM',
			        '23:00': '11:00 PM',
			        '23:30': '11:30 PM'
			    }

			    return times[time]
			},
		}
	}
</script>