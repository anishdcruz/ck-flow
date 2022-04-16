<template>
	<div class="content-inner">
	  <x-modal width="850" :loading="loading" @cancel="$emit('cancel')">
	    <div slot="title">
	    	{{$t('media_library')}}
	    	<x-button type="primary" size="sm" @click="showUploadModal = true">{{$t('upload')}}</x-button>
	    </div>
	    <div v-if="show">
	    	<x-table class="table table-link">
	    	  <thead>
	    	    <x-tr>
	    	    	<x-td type="th" size="2">{{$t('id')}}</x-td>
	    	    	<x-td type="th" size="2">{{$t('image')}}</x-td>
	    	    	<x-td type="th" size="14">{{$t('title')}}</x-td>
	    	    	<x-td type="th" size="3">{{$t('size')}}</x-td>
	    	    	<x-td type="th" size="3">{{$t('created_at')}}</x-td>
	    	    </x-tr>
	    	  </thead>
	    	  <x-tbody v-if="collection.data && collection.data.length">
	    	    <x-tr v-for="item in collection.data"
	    	    	:key="item.id" @click.native="select(item)">
	    	    	<x-td>{{item.id}}</x-td>
	    	    	<x-td>
	    	    		<img :src="item.filename" class="index-thumb">
	    	    	</x-td>
	    	    	<x-td>{{item.title}}</x-td>
	    	    	<x-td>{{item.size}}</x-td>
	    	    	<x-td>{{item.created_at | toDate}}</x-td>
	    	    </x-tr>
	    	  </x-tbody>
	    	  <x-tbody v-else>
	    	    <x-tr>
	    	      <x-td colspan="10">
	    	        <div class="table-no_results">{{$t('no_results_found')}}</div>
	    	      </x-td>
	    	    </x-tr>
	    	  </x-tbody>
	    	</x-table>
	    </div>
	    <template class="flex flex-between flex-center" slot="footer">
	      <div v-if="show">
	          <small>{{$t('showing', {from: collection.from || 0, to: collection.to || 0, total: collection.total})}}</small>
	      </div>
	      <div v-if="show">
          <x-button size="sm" :disabled="loading || !collection.prev_page_url"
            @click="prevPage">
              &laquo; {{$t('prev')}}
            </x-button>
          <x-button size="sm" :disabled="loading || !collection.next_page_url"
            @click="nextPage">
              {{$t('next')}} &raquo;
            </x-button>
	      </div>
	    </template>
	  </x-modal>
	  <upload v-if="showUploadModal" @cancel="onUploadClose" @saved="onSaved"></upload>
	</div>
</template>
<script>
	import { modalable } from '@js/lib/mixins'
	import upload from '@js/views/images/upload.vue'

	export default {
		mixins: [modalable],
		components: { upload },
		data() {
			return {
				url: 'images',
				title: 'media_library',
				resource: 'images',
				show: false,
				showUploadModal: false,
				loading: false,
				query: {
					limit: 6
				}
			}
		},

		methods: {
			select(item) {
				this.$emit('select', {
				  target: {
				    value: item
				  }
				})
			},
			onSaved() {
				this.onUploadClose()
				this.fetch()
			},
			toggleUploadModal() {
				this.showUploadModal = true
			},
			onUploadClose() {
				this.showUploadModal = false
			},
			nextPage () {
		    if(this.collection.next_page_url) {
	        this.query.page = Number(this.query.page) + 1
	        this.fetch()
		    }
			},
			prevPage() {
		    if(this.collection.prev_page_url) {
	        this.query.page = Number(this.query.page) - 1
	        this.fetch()
		    }
			},
			setData(res) {
			  this.$set(this.$data, 'collection', res.data.collection)
			  this.show = true
			  this.loading = false
			}
		}
	}
</script>