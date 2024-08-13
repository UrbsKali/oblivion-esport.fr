<script>
	import UserBadge from '$lib/components/UserBadge.svelte';
	import { config } from '$lib/config';
	import { onMount } from 'svelte';

	let url = '/admin';

	onMount(() => {
		url = window.location.origin + config.basePath + '/admin';
		console.log(config.basePath);
		console.log(url);
	});
</script>

<section>
	<h1>
		Je cook sur la page <a href={url}>/admin</a>, on va refaire le reste du site juste après
	</h1>
	<div class=" absolute top-3 right-5">
		<UserBadge />
	</div>
</section>

<style>
</style>
