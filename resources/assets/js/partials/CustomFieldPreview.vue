<template>
  <x-col span="8">
    <div class="info-group">
      <label class="info-label">
        {{field.label}}
      </label>
      <div v-if="field.type === 'date'">
        <span v-if="field.model">{{field.model | formatDate(field.format)}}</span>
        <span v-else>-</span>
      </div>
      <div v-else-if="field.type === 'image'">
        <div v-if="field.model" class="field-image">
          <img class="thumb-image" :src="field.model">
        </div>
        <div v-else>-</div>
      </div>
      <div v-else-if="field.type === 'currency'">
        <div v-if="field.model">{{field.model | formatCurrency(field.currency)}}</div>
        <div v-else>-</div>
      </div>
      <div v-else-if="field.type === 'textarea'">
        <pre>{{field.model || '-'}}</pre>
      </div>
      <div v-else>{{field.model || '-'}}</div>
    </div>
  </x-col>
</template>
<script>
	export default {
		props: {
			field: Object
		}
	}
</script>