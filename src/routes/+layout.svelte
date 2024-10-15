<script>
	import '../app.css';
	import Topbar from '$lib/components/share/Topbar.svelte';
	import { page } from '$app/stores';

	let is_on_admin_route = false;

	page.subscribe((value) => {
		is_on_admin_route = value?.route?.id?.includes('admin');
	});
</script>

<svelte:head>
	<title>Oblivion Esport</title>
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<link rel="icon" type="image/png" href="/v2/oblivion.png" />
</svelte:head>

<div class="min-h-screen antialiased text-white bg-gray-900 min-w-screen bg-opacity-80">
	{#if !is_on_admin_route}
		<Topbar />
	{/if}
	<slot />
</div>

<style>
</style>
