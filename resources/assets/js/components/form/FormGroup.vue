<template>
	<x-col :span="col" :offset="offset" v-if="col">
	  <div class="form-group">
	  	<label class="form-label">
	  		<slot name="label" v-if="$slots.label"></slot>
	  		<template v-else>
	  			<span>{{label}}</span>
	  			<small class="form-optional" v-if="optional">({{$t('optional')}})</small>
	  		</template>
	  	</label>
      <slot v-if="$slots.default"></slot>
      <component :is="`x-${source}`" v-bind="$props" @input="handleInput" v-else></component>
			<small v-if="errors && errors.length" class="error-input">{{errors[0]}}</small>
	  </div>
	</x-col>
  <div class="form-group" v-else>
  	<label class="form-label">
  		<slot name="label" v-if="$slots.label"></slot>
  		<template v-else>
  			<span>{{label}}</span>
  			<small class="form-optional" v-if="optional">({{$t('optional')}})</small>
  		</template>
  	</label>
    <slot v-if="$slots.default"></slot>
  	<component :is="`x-${source}`" v-bind="$props" @input="handleInput" v-else></component>
		<small v-if="errors && errors.length" class="error-input">{{errors[0]}}</small>
  </div>
</template>
<script>
  export default {
  	name: 'XFormGroup',
  	model: {
  	  prop: 'value',
  	  event: 'input'
  	},
  	props: {
  		type: {
  			default: 'text'
  		},
  	  value: [String, Number, Object, Array, Boolean],
      options: Array,
      tabindex: [String, Number],
  	  placeholder: String,
  	  disabled: {
  	    type: Boolean,
  	    default: false
  	  },
  	  optional: {
  	  	type: Boolean,
  	  	default: false
  	  },
      source: {
        type: String,
        default: 'input'
      },
  	  errors: {
        default: null
      },
  	  label: String,
  	  col: {
  	  	default: null
  	  },
  	  offset: {
  	  	default: null
  	  },
      size: {
        default: null
      }
  	},
  	methods: {
  		handleInput(input) {
  			this.$emit('input', input)
  		}
  	}
  }
</script>