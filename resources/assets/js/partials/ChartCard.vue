<template>
	<div class="metric-card">
		<div class="metric-card-inner">
      <div class="metric-card-title">
        <span>{{label}}</span>
        <i class="metric-card-trash icon icon-trash-b"
          v-if="edit"
          @click="$emit('remove')"></i>
      </div>
			<la-cartesian :width="275" :height="45" :data="values">
			  <la-line :color="color" v-if="type === 'line'" prop="value"></la-line>
      	<la-area :color="color" v-else-if="type === 'area'" prop="value"></la-area>
      	<la-bar :color="color" v-else prop="value"></la-bar>
			</la-cartesian>
		</div>
	</div>
</template>

<script>
import { Cartesian, Area, Line, Bar } from 'laue'

export default {
  components: {
    LaCartesian: Cartesian,
    LaLine: Line,
    LaBar: Bar,
    LaArea: Area
  },
  props: {
  	label: String,
  	values: Array,
  	type: String,
    edit: {
      type: Boolean,
      default: false
    },
    color: {
      default: null
    }
  }
}
</script>