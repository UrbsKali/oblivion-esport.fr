<script>
	// @ts-nocheck
	import { onMount } from 'svelte';
	import { supabase } from '$lib/supabaseClient';
	import LinkButton from '$lib/components/LinkButton.svelte';
	import { pushState } from '$app/navigation';

	let stats = {};
	let name = '';
	let completion = [];

	let isFocus = false;

	onMount(async () => {
		// check url date parameter
		const urlParams = new URLSearchParams(window.location.search);
		const params = urlParams.get('name');
		if (params) {
			fetchInfo(params);
			name = params;
		}

		const search = document.querySelector('#simple-search');
		search.addEventListener('focus', (e) => {
			isFocus = true;
			console.log(isFocus);
		});
		search.addEventListener('blur', (e) => {
			isFocus = false;
			console.log(isFocus);
		});
	});

	async function fetchInfo(name) {
		stats = {};
		// get matchs
		const { data, error } = await supabase
			.from('Stats')
			.select(`id, player_name, stats`)
			.eq('player_name', name)
			.single();

		stats = data.stats;
		console.log(stats);
		// update url to current date, without reloading the page
		pushState(
			`${window.location.origin}${window.location.pathname}?name=${encodeURIComponent(name)}`
		);
	}

	async function getCompletion(name) {
		const { data, error } = await supabase
			.from('Stats')
			.select(`player_name`)
			.ilike('player_name', `${name}*`)
			.limit(5);

		completion = data.map((el) => el.player_name);
	}

	function prettyRound(value) {
		if (value > 1000) {
			value = (value / 1000).toFixed(1) + 'k';
		} else if (value > 1000000) {
			value = (value / 1000000).toFixed(1) + 'M';
		}
		return value;
	}

	$: getCompletion(name);
</script>

<svelte:head>
	<title>Statistiques | Oblivion Esport</title>
	<meta name="description" content="Statistiques des joueurs - Oblivion Esport" />
	<meta name="keywords" content="Stats, LoL, League of Legends, Tournaments" />
	<meta name="author" content="UrbsKali" />
	<meta name="robots" content="index, follow" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<meta name="theme-color" content="#000000" />
	<meta name="msapplication-TileColor" content="#000000" />
	<meta name="msapplication-TileImage" content="https://oblivion-esport.fr/oblivion.png" />
	<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="apple-mobile-web-app-title" content="Statistiques" />
	<meta name="application-name" content="Statistiques" />
	<meta name="msapplication-navbutton-color" content="#000000" />
	<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
	<meta name="og:title" content="Statistiques" />
	<meta name="og:description" content="Statistiques des joueurs - Oblivion Esport" />
	<meta name="og:image" content="/oblivion.png" />
	<meta name="og:url" content="https://oblivion-esport.fr/v2/stats" />
	<meta name="og:site_name" content="Oblivion Esport" />
</svelte:head>

<!-- Hero Search text input-->
<div></div>
<div class="flex w-screen justify-center items-center flex-col">
	<h1 class="text-4xl font-semibold text-gray-900 dark:text-white">Rechercher un joueur</h1>
	<div class="w-96 p-5">
		<div class="relative w-full">
			<div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
				<svg
					aria-hidden="true"
					class="w-5 h-5 text-gray-500 dark:text-gray-400"
					fill="currentColor"
					viewbox="0 0 20 20"
					xmlns="http://www.w3.org/2000/svg"
				>
					<path
						fill-rule="evenodd"
						d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
						clip-rule="evenodd"
					/>
				</svg>
			</div>
			<input
				type="text"
				id="simple-search"
				class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
				placeholder="KcKitt_#EUW"
				required=""
				bind:value={name}
			/>
			{#if isFocus || !(completion.length == 1 && name == completion[0])}
				<div
					class=" absolute z-10 mt-1 bg-gray-50 w-full border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block pl-10 p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
				>
					{#each completion as c}
						<button
							on:click={(e) => {
								name = c;
								fetchInfo(c);
							}}
						>
							{c}
						</button>
						<br />
					{/each}
				</div>
			{/if}
		</div>
	</div>
</div>
{#if stats.kills}
	<div class="">
		<section class="bg-white dark:bg-gray-900">
			<div class="max-w-screen-xl px-4 py-8 mx-auto text-center lg:py-16 lg:px-6">
				<dl class="grid max-w-screen-md gap-8 mx-auto text-gray-900 sm:grid-cols-3 dark:text-white">
					<div class="flex flex-col items-center justify-center">
						<dt class="mb-2 text-3xl md:text-4xl font-extrabold">{stats.kills}</dt>
						<dd class="font-light text-gray-500 dark:text-gray-400">Kills</dd>
					</div>
					<div class="flex flex-col items-center justify-center">
						<dt class="mb-2 text-3xl md:text-4xl font-extrabold">{stats.deaths}</dt>
						<dd class="font-light text-gray-500 dark:text-gray-400">Deaths</dd>
					</div>
					<div class="flex flex-col items-center justify-center">
						<dt class="mb-2 text-3xl md:text-4xl font-extrabold">{stats.assists}</dt>
						<dd class="font-light text-gray-500 dark:text-gray-400">Assits</dd>
					</div>
				</dl>
				<br />
				<dl class="grid max-w-screen-md gap-8 mx-auto text-gray-900 sm:grid-cols-3 dark:text-white">
					<div class="flex flex-col items-center justify-center">
						<dt class="mb-2 text-3xl md:text-4xl font-extrabold">
							{prettyRound(stats.damageToChampions)}
						</dt>
						<dd class="font-light text-gray-500 dark:text-gray-400">
							Dégats Infligés aux Champions
						</dd>
					</div>
					<div class="flex flex-col items-center justify-center">
						<dt class="mb-2 text-3xl md:text-4xl font-extrabold">{stats.minions}</dt>
						<dd class="font-light text-gray-500 dark:text-gray-400">Minions Tués</dd>
					</div>
					<div class="flex flex-col items-center justify-center">
						<dt class="mb-2 text-3xl md:text-4xl font-extrabold">{prettyRound(stats.totalGold)}</dt>
						<dd class="font-light text-gray-500 dark:text-gray-400">Gold Amassés</dd>
					</div>
				</dl>
				<br />
				<dl class="grid max-w-screen-md gap-8 mx-auto text-gray-900 sm:grid-cols-3 dark:text-white">
					<div class="flex flex-col items-center justify-center">
						<dt class="mb-2 text-3xl md:text-4xl font-extrabold">
							{stats.win}
						</dt>
						<dd class="font-light text-gray-500 dark:text-gray-400">Victoires</dd>
					</div>
					<div class="flex flex-col items-center justify-center">
						<dt class="mb-2 text-3xl md:text-4xl font-extrabold">{stats.lose}</dt>
						<dd class="font-light text-gray-500 dark:text-gray-400">Défaites</dd>
					</div>
					<div class="flex flex-col items-center justify-center">
						<dt class="mb-2 text-3xl md:text-4xl font-extrabold">{stats.total}</dt>
						<dd class="font-light text-gray-500 dark:text-gray-400">Matchs joués</dd>
					</div>
				</dl>
			</div>
		</section>
	</div>
{/if}

<style>
</style>
