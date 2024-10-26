<script>
	// get url slug from router
	import { page } from '$app/stores';
	import { supabase } from '$lib/supabaseClient';

	import { userdata } from '$lib/store';

	import SvelteMarkdown from 'svelte-markdown';
	import { renderers } from '$lib/renderers';
	import CrudForm from '$lib/components/modals/CrudForm.svelte';
	import SucessModal from '$lib/components/modals/SucessModal.svelte';

	let slug = '';
	let tournament = {};
	let user;

	userdata.subscribe((value) => {
		if (value) {
			user = value;
		}
	});

	page.subscribe(async (value) => {
		slug = value.params.slug;
		await loadPage();
	});

	async function loadPage() {
		const { data, error } = await supabase
			.from('Tournaments')
			.select('id, title, start, end, slug(body, image, last_update), can_register')
			.eq('slug', slug)
			.single();

		console.log(data);
		if (error) {
			console.error('error', error);
		} else {
			tournament = data;
		}
	}
</script>

<div class="min-h-screen">
	<div
		id="hero"
		style="background-image: linear-gradient(0deg, rgba(14, 19, 31, 1) 0%, rgba(14, 19,31, 0.5) 100%), url({tournament
			?.slug?.image});"
		class="relative h-screen bg-center bg-no-repeat bg-cover"
	></div>
	<div class="container flex items-center justify-center px-4 align-middle sm:mx-auto">
		<div class="w-10/12 p-5 mt-[-25%] border border-gray-700 rounded-lg backdrop-blur-lg">
			<h1 class="text-4xl font-bold">{tournament?.title || slug}</h1>
			<p class="text-lg text-gray-500">// {tournament?.start || ''} - {tournament?.end || ''}</p>
			<SvelteMarkdown source={tournament?.slug?.body} {renderers} />
			<span>
				{#if tournament?.can_register}
					<button
						class="px-4 py-2 mt-5 text-white rounded-md bg-primary-500"
						on:click={() => {
							new CrudForm({
								target: document.body,
								props: {
									title: 'Inscription',
									fields: [
										{
											type: 'info',
											text: "Vous êtes sur le point de vous inscrire à ce tournoi. Veuillez sélectionner l'équipe avec laquelle vous souhaitez participer, Vous devez être l'owner de ce groupe. Si vous n'avez pas d'équipe, vous pouvez en créer une en allant sur la page de votre profil.",
											wide: true
										},
										{
											id: 'team',
											name: 'Équipe',
											type: 'autocomplete',
											label: 'Équipe',
											wide: true,
											onChange: async (e) => {
												// search through users
												const { data, error } = await supabase
													.from('Teams')
													.select('id, name, logo_url, member_of!inner(uid, role)')
													.eq('member_of.uid', user.id)
													.eq('member_of.role', 'owner')
													.ilike('name', `${e.target.value}*`)
													.range(0, 4);
												if (error) {
													console.error(error);
													return;
												}
												// create options
												let options = [];
												for (let i = 0; i < data.length; i++) {
													let el = data[i];
													options.push({ value: el.id, text: el.name, image: el.logo_url });
												}
												if (options.length === 0 && e.target.value !== '') {
													options.push({ value: '', text: 'Aucun résultat' });
												}
												return options;
											}
										},
										{
											id: 'why',
											name: 'Pourquoi vous ?',
											type: 'textarea',
											wide: true
										}
									],
									onSubmit: async (e) => {
										// get forms data
										e.preventDefault();
										const form_data = new FormData(e.target.closest('form'));
										let data = {};
										for (let [key, value] of form_data.entries()) {
											if (key == 'team') {
												data[key.toLowerCase()] = document.querySelector(
													`#${key}`
												).previousElementSibling.dataset.utils;
											} else {
												data[key.toLowerCase()] = value;
											}
										}
										console.log(data);
										const { error } = await supabase.from('part_of').insert({
											team_id: data.team,
											why: data.why,
											tournament_id: tournament.id
										});
										if (error) {
											console.error(error);
											if (
												error.message.includes('duplicate key value violates unique constraint')
											) {
												alert('Vous êtes déjà inscrit à ce tournoi.');
											}
											return;
										}
										new SucessModal({
											target: document.body,
											props: {
												title: 'Inscription réussie !',
												message:
													'Votre inscription a bien été prise en compte. Vous êtes en attente de validation.'
											}
										});
									}
								}
							});
						}}>Inscription</button
					>
				{:else}
					<button class="px-4 py-2 mt-5 text-white bg-gray-500 rounded-md"
						>Inscription fermée</button
					>
				{/if}
			</span>
		</div>
	</div>
</div>
