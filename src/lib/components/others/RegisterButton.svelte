<script>
	import { supabase } from '$lib/supabaseClient';
	import { userdata } from '$lib/store';
	import { goto } from '$app/navigation';

	import CrudForm from '$lib/components/modals/CrudForm.svelte';
	import SucessModal from '$lib/components/modals/InfoModal.svelte';
	import InfoModal from '$lib/components/modals/InfoModal.svelte';

	export let tournament_id = 0;

	let user;

	userdata.subscribe((value) => {
		if (value) {
			user = value;
		}
	});
</script>

<button
	class="px-4 py-2 mt-5 text-white rounded-md bg-primary-500"
	on:click={async (e) => {
		e.preventDefault();
		e.stopPropagation();
		// check if user is logged in
		if (!user) {
			console.log('not logged in');
			new InfoModal({
				target: document.body,
				props: {
					message: 'Vous devez être connecté pour vous inscrire à un tournoi.',
					type: 'info',
					onClose: () => {},
					action: [
						{
							text: 'Se connecter',
							callback: () => {
								goto('/login');
							},
							is_main: false
						},
						{
							text: 'Créer un compte',
							callback: () => {
								goto('/register');
							},
							is_main: true
						}
					]
				}
			});
			return;
		}

		// if user is not owner of a team, show error
		const { data, error } = await supabase
			.from('Teams')
			.select('member_of!inner(uid, role)')
			.eq('member_of.uid', user.id)
			.eq('member_of.role', 'owner');
		if (error) {
			console.error(error);
			alert('Une erreur est survenue.');
			return;
		}
		if (data.length === 0) {
			new InfoModal({
				target: document.body,
				props: {
					message: "Vous devez être propriétaire d'une équipe pour vous inscrire à un tournoi.",
					type: 'info',
					onClose: () => {},
					action: [
						{
							text: 'Créer une équipe',
							callback: () => {
								goto('/user/teams');
							},
							is_main: true
						}
					]
				}
			});
			return;
		}

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
						},
						onSelect: (e) => {}
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
							data[key.toLowerCase()] = document.querySelector('label[for="team"]').dataset.utils;
						} else {
							data[key.toLowerCase()] = value;
						}
					}
					console.log(data);

					// check if team can be registered, with rpc
					const { data: rpc_data, error: rpc_error } = await supabase.rpc('check_team_criteria', {
						t_id: data.team
					});
					if (rpc_error) {
						console.error(rpc_error);
						alert('Une erreur est survenue.');
						return;
					}
					if (!rpc_data) {
						new InfoModal({
							target: document.body,
							props: {
								message:
									"Votre équipe ne remplit pas les critères pour s'inscrire à ce tournoi. Il faut que chacun de vos membres (au moins 5) ait rempli son profil.",
								type: 'error',
								onClose: () => {},
								action: [
									{
										text: 'Modifer mon équipe',
										callback: () => {
											// remove the CrudForm
											document.querySelector('#CrudModal').remove();
											// go to team page
											goto(`/user/teams/${data.team}`);
										},
										is_main: true
									}
								]
							}
						});
						return;
					}

					const { error } = await supabase.from('part_of').insert({
						team_id: data.team,
						why: data.why,
						tournament_id: tournament_id
					});
					if (error) {
						console.error(error);
						if (error.message.includes('duplicate key value violates unique constraint')) {
							alert('Vous êtes déjà inscrit à ce tournoi.');
						} else {
							alert('Une erreur est survenue.');
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
