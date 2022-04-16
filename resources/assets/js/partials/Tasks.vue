<template>
	<div class="tasks">
		<div class="tasks-title">
			{{$t('tasks')}}
		</div>
		<div class="tasks-extra">
			<a href="#">All</a>
			<a href="#">Over Due</a>
			<a href="#">Today</a>
			<a href="#">Tomorrow</a>
		</div>
	</div>
</template>
<script>
	export default {
		name: 'XTasks',
		props: {
			type: String,
			id: Number
		}
	}
</script>