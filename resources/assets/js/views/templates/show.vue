<template>
	<div class="content-inner" v-if="show">
		<x-panel padding margin>
			<div slot="title">
				<router-link to="/templates">{{$t('template')}}</router-link> / {{model.name}}
			</div>
			<div slot="extra">
				<router-link :to="`/templates`" class="btn btn-default btn-sm">
					<small class="icon icon-arrow-left-c"></small>
				</router-link>
				<x-dropdown size="sm" dir="right" icon="more">
					<x-dropdown-menu slot="menu">
						<x-dropdown-item>
							<a target="_blank" :href="`/api/templates/${model.id}/export`">
								{{$t('export')}}
							</a>
						</x-dropdown-item>
						<x-dropdown-link divide :to="`/templates/${model.id}/edit`">
							{{$t('edit')}}
						</x-dropdown-link>
						<x-dropdown-item>
							<a @click.stop="removeDB('templates', model.id)">{{$t('delete')}}</a>
						</x-dropdown-item>
					</x-dropdown-menu>
				</x-dropdown>
			</div>
			<x-row line>
				<x-group col="8" label="name">
					<p>{{model.name}}</p>
				</x-group>
				<x-group col="8" label="type">
					<p v-if="model.type">
						{{model.type}}
					</p>
				</x-group>
				<x-group col="8" label="page_size">
					<p v-if="model.page_size">
						{{model.page_size}}
					</p>
				</x-group>
			</x-row>
			<x-row line>
				<x-group col="8" label="orientation">
					<p>{{model.orientation}}</p>
				</x-group>
				<x-group col="8" label="header_height">
					<p v-if="model.header_height">
						{{model.header_height}}
					</p>
				</x-group>
				<x-group col="8" label="footer_height">
					<p v-if="model.footer_height">
						{{model.footer_height}}
					</p>
				</x-group>
			</x-row>
			<x-row line>
				<x-group col="8" label="created_at">
					<pre>{{model.created_at | toDate}}</pre>
				</x-group>
			</x-row>
		</x-panel>
		<x-panel>
			<div slot="title">{{$t('preview')}}</div>
			<div slot="extra">
				<a target="_blank" :href="`/api/templates/${model.id}/preview`" class="btn btn-default btn-sm">
					<small class="icon icon-android-download"></small>
				</a>
			</div>
			<div class="template-preview">
				<object :data="`/api/templates/${model.id}/preview`" type="application/pdf" width="100%" height="100%">
				</object>
			</div>
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
					pages: [],
				}
			}
		}
	}
</script>