<script>
	import { supabase } from '$lib/supabaseClient';

	let tournaments = [];
	let search = '';

	let selectedTournament = null;

	async function getCompletion(search) {
		if (search.length > 2) {
			const { data, error } = await supabase
				.from('Tournaments')
				.select('slug, title')
				.ilike('title', `${search}*`)
				.range(0, 4);
			if (error) {
				console.error(error);
			} else {
				tournaments = data;
			}
		}
	}

	$: getCompletion(search);

	function selecTournament(slug) {
		selectedTournament = tournaments.find((tournament) => tournament.slug === slug);
	}

	import { Carta, MarkdownEditor } from 'carta-md';
	import { attachment } from '@cartamd/plugin-attachment';
	import { emoji } from '@cartamd/plugin-emoji';
	import { slash } from '@cartamd/plugin-slash';
	import { code } from '@cartamd/plugin-code';

	const carta = new Carta({
		sanitizer: false,
		extensions: [
			attachment({
				async upload() {
					return 'some-url-from-server.xyz';
				}
			}),
			emoji(),
			slash(),
			code()
		]
	});

	export let value = `This is an example inspired by [GitHub](https://github.com)
\`\`\`js
console.log('Hello, World!');
\`\`\``;
</script>

<section>
	<h1>Informations des Tournois</h1>
	{#if selectedTournament == null}
		<!--Autocomplete search in Tournaments table-->
		<input
			type="text"
			id="search"
			bind:value={search}
			placeholder="Rechercher un tournoi"
			class="w-full p-2 mb-4 bg-gray-900 border-2 border-gray-700 rounded-md"
		/>
		<ul>
			{#each tournaments as tournament}
				<li class="flex items-center justify-between p-2 mb-2 bg-gray-800 rounded-md">
					{tournament.title}
					<button on:click={() => selecTournament(tournament.slug)}>Choisir</button>
				</li>
			{/each}
		</ul>
	{:else}
		<!--Display tournament informations, and markdown editor-->
		<h2>{selectedTournament.title}</h2>
		<p>{selectedTournament.slug}</p>

		<!--Markdown editor-->
		<MarkdownEditor bind:value mode="tabs" theme="github" {carta} />
	{/if}
</section>
