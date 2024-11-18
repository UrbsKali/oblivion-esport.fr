<script>
	import { supabase } from '$lib/supabaseClient';

	import { Carta, MarkdownEditor } from 'carta-md';
	import { attachment } from '@cartamd/plugin-attachment';
	import { emoji } from '@cartamd/plugin-emoji';
	import { slash } from '@cartamd/plugin-slash';
	import { code } from '@cartamd/plugin-code';

	import '$lib/styles/github.scss';

	let tournaments = [];
	let search = '';

	let value = '';
	let description = '';
	let full_body = {};

	let currentTab = '';

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

	async function selecTournament(slug) {
		selectedTournament = tournaments.find((tournament) => tournament.slug === slug);
		const { data, error } = await supabase
			.from('Tournaments')
			.select('slug(slug, body, description, image), can_register, title, start, end')
			.eq('slug', slug)
			.single();

		if (error) {
			console.error(error);
		} else {
			full_body = data.slug.body;
			value = full_body[Object.keys(full_body)[0]];
			description = data.slug.description;
			selectedTournament = data;
			currentTab = Object.keys(full_body)[0];
		}
	}

	const carta = new Carta({
		sanitizer: false,
		theme: 'github-dark',
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
		<p>{selectedTournament.slug.slug}</p>

		<!--Description-->
		<br />
		<label for="description">Description</label>
		<textarea
			bind:value={description}
			class="w-full p-2 mb-4 bg-gray-900 border-2 border-gray-700 rounded-md"
		></textarea>

		<!-- Tab for select the right body part -->
		<ul class="flex justify-center my-2">
			{#each Object.keys(full_body) as key}
				<li
					class="p-2 mx-2 text-white rounded-md cursor-pointer bg-primary-500"
					on:click={() => {
						full_body[currentTab] = value;
						value = full_body[key];
						currentTab = key;
					}}
				>
					{key}
				</li>
			{/each}
			<li
				class="p-2 mx-2 text-white rounded-md cursor-pointer bg-primary-500"
				on:click={() => {
					let name = prompt('Nom du nouvel onglet');
					const newTab = name.replace(/\s/g, '_').toLowerCase();
					full_body[newTab] = '';
					value = '';
					currentTab = newTab;
				}}
			>
				+
			</li>
		</ul>
		<!--Markdown editor-->
		<MarkdownEditor mode="tabs" theme="github" {carta} bind:value />

		<!--Save button-->
		<button
			class="p-2 mt-4 text-white rounded-md bg-primary-500"
			on:click={async () => {
				// update the body of the selected tournament
				full_body[currentTab] = value;
				const { error } = await supabase
					.from('tournaments_info')
					.update({ body: full_body, description: description })
					.eq('slug', selectedTournament.slug.slug);
				if (error) {
					console.error(error);
				} else {
					console.log('Tournament updated');
					alert('Tournoi mis à jour');
				}
			}}>Enregistrer</button
		>
	{/if}
</section>

<style>
</style>
