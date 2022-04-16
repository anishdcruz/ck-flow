<template>
	<x-col :span="col" :offset="offset" v-if="col">
	  <div class="info-group">
	  	<label class="info-label">
	  		<slot name="label" v-if="$slots.label"></slot>
	  		<template v-else>
	  			<span>{{$t(label)}}</span>
	  		</template>
	  	</label>
      <slot v-if="$slots.default"></slot>
	  </div>
	</x-col>
  <div class="info-group" v-else>
  	<label class="info-label">
  		<slot name="label" v-if="$slots.label"></slot>
  		<template v-else>
  			<span>{{$t(label)}}</span>
  		</template>
  	</label>
    <slot v-if="$slots.default"></slot>
  </div>
</template>
<script>
  export default {
  	name: 'XGroup',
  	model: {
  	  prop: 'value',
  	  event: 'input'
  	},
  	props: {
  		tag: {
  			type: String,
  			default: 'p'
  		},
  	  label: String,
  	  col: {
  	  	default: null
  	  },
  	  offset: {
  	  	default: null
  	  }
  	}
  }
</script>