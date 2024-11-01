<script>
	import '../app.css';
	import Topbar from '$lib/components/share/Topbar.svelte';
	import { page } from '$app/stores';
	import Footer from '$lib/components/share/Footer.svelte';

	let is_on_admin_route = false;

	page.subscribe((value) => {
		is_on_admin_route = value?.route?.id?.includes('admin');
	});
</script>

<svelte:head>
	<title>Oblivion Esport</title>
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<link rel="icon" type="image/png" href="/assets/oblivion.png" />
	<!--SEO & Twiter OGG-->
	<meta property="og:title" content="Oblivion Esport" />
	<meta property="og:description" content="Vivez l'expérience e-sport" />
	<meta property="og:image" content="/assets/card_main.avif" />
	<meta property="og:url" content="https://oblivion-esport.fr" />
	<meta name="twitter:card" content="summary_large_image" />
	<meta name="twitter:title" content="Oblivion Esport" />
	<meta name="twitter:description" content="Vivez l'expérience e-sport" />
	<meta name="twitter:image" content="/assets/card_main.avif" />
</svelte:head>

<div class="min-h-screen antialiased text-white bg-gray-900 min-w-screen bg-opacity-80">
	{#if !is_on_admin_route}
		<Topbar />
	{/if}
	<slot />
</div>

<style>
</style>
