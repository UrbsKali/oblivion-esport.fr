<script>
	import { supabase } from '$lib/supabaseClient';
	import { page } from '$app/stores';
	import { goto } from '$app/navigation';

	import { Carta, MarkdownEditor } from 'carta-md';
	import { attachment } from '@cartamd/plugin-attachment';
	import { emoji } from '@cartamd/plugin-emoji';
	import { slash } from '@cartamd/plugin-slash';
	import { code } from '@cartamd/plugin-code';

	import '$lib/styles/github.scss';

	let slug;

	let tournament = {};

	let value = '';
	let description = '';
	let full_body = {};
	let currentTab = '';

	page.subscribe(async (value) => {
		if (value) {
			slug = value.params.slug;
			await loadPage();
		}
	});

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

	async function loadPage() {
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
			tournament = data;
			currentTab = Object.keys(full_body)[0];
		}
	}
</script>

<div class="flex flex-col items-center justify-center px-5 py-0 mx-auto sm:p-0">
	<div class="flex items-center justify-start w-full mb-5">
		<button
			class="flex items-center justify-center w-10 h-10 text-gray-200 bg-gray-800 border border-gray-700 rounded-full shadow hover:bg-opacity-10"
			on:click={() => goto('/admin/tournaments', { replaceState: true })}
		>
			<svg
				class="w-5 h-5"
				aria-hidden="true"
				fill="currentColor"
				viewbox="0 0 20 20"
				xmlns="http://www.w3.org/2000/svg"
			>
				<path
					fill-rule="evenodd"
					d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
					clip-rule="evenodd"
				/>
			</svg>
		</button>
		<h2 class="text-2xl font-bold text-center text-gray-200" id="teamName">
			{tournament.title || slug}
		</h2>
		<button
			class="flex items-center justify-center w-10 h-10 text-gray-200 bg-gray-800 border border-gray-700 rounded-full shadow hover:bg-opacity-10"
			on:click={async (e) => {
				// delete team
				const confirmation = prompt('Voulez-vous vraiment supprimer cette équipe ? (oui/non)');
				if (confirmation !== 'oui') return;
				const { data, error } = await supabase.from('Tournaments').delete().eq('slug', slug);
				if (error) {
					console.error(error);
					alert("Une erreur est survenue lors de la suppression de l'équipe");
					return;
				}
				goto('/admin/tournaments', { replaceState: true });
			}}
		>
			<!--Trash icon-->
			<svg
				class="w-5 h-5 text-white hover:text-red-500"
				aria-hidden="true"
				xmlns="http://www.w3.org/2000/svg"
				width="24"
				height="24"
				fill="none"
				viewBox="0 0 24 24"
			>
				<path
					stroke="currentColor"
					stroke-linecap="round"
					stroke-linejoin="round"
					stroke-width="2"
					d="M5 7h14m-9 3v8m4-8v8M10 3h4a1 1 0 0 1 1 1v3H9V4a1 1 0 0 1 1-1ZM6 7h12v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7Z"
				/>
			</svg>
		</button>
	</div>
	<div class="w-full pt-2 border-t border-gray-700">
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
					.eq('slug', slug);
				if (error) {
					console.error(error);
				} else {
					console.log('Tournament updated');
					alert('Tournoi mis à jour');
				}
			}}>Enregistrer</button
		>
	</div>
</div>

<style>
	#teamName {
		width: calc(100% - 80px);
	}
</style>
