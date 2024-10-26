<script>
	import { supabase } from '$lib/supabaseClient';
	import { userdata } from '$lib/store';

	import CrudForm from '$lib/components/modals/CrudForm.svelte';
	import SucessModal from '$lib/components/modals/SucessModal.svelte';

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
						tournament_id: tournament_id
					});
					if (error) {
						console.error(error);
						if (error.message.includes('duplicate key value violates unique constraint')) {
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
