<script>
	import { onMount } from 'svelte';
	import { createAdminClient, supabase, supabaseUrl } from '$lib/supabaseClient';
	import { createClient } from '@supabase/supabase-js';
	import { formatText } from '$lib/utils';

	import CrudForm from '$lib/components/modals/CrudForm.svelte';
	import SucessModal from '$lib/components/modals/InfoModal.svelte';
	import Table from '$lib/components/admin/Table.svelte';
	import ReadModal from '$lib/components/modals/ReadModal.svelte';

	let headers = ['Nom', 'Permissions', 'Actions'];

	let dbInfo = {
		table: 'profiles',
		key: 'id, username, avatar_url, perms!inner(permissions)'
	};

	let service_key = '';

	let filters = [];
	function parseItems(data) {
		let items = [];
		data.forEach((el) => {
			const avatar = el.avatar_url || '/assets/oblivion.webp';
			items.push([
				{ value: el.username, data: el.id, avatar: avatar },
				{ value: formatText(el.perms.permissions) || 'Aucune permission' }
			]);
		});
		return items;
	}

	let actions = [
		{
			type: 'view',
			handler: async (e) => {
				e.preventDefault();
				let tr = e.target.closest('tr');
				let id = tr.children[0].dataset.utils;
				let name = tr.children[0].innerText;

				const { data, error } = await supabase
					.from('perms')
					.select('permissions')
					.eq('user_id', id)
					.single();

				if (error) {
					console.error(error);
					alert('An error occured while fetching the user');
					return;
				}

				let perms = data.permissions || [];

				new ReadModal({
					target: document.body,
					props: {
						values: {
							header: {
								title: name
							},
							body: [
								{
									label: 'Permissions',
									value: perms.join(', ')
								}
							]
						},
						actions: [
							{
								type: 'edit',
								title: 'Modifier',
								handler: () => {
									new CrudForm({
										target: document.body,
										props: {
											title: 'Modifier les permissions',
											fields: [
												{
													name: 'Accès au panel admin',
													type: 'checkbox',
													required: true,
													checked: perms.includes('panel_admin'),
													value: 'panel_admin'
												},
												{
													name: 'Accès au inscription',
													type: 'checkbox',
													required: true,
													checked: perms.includes('register'),
													value: 'register'
												},
												{
													name: 'Accès au MatchID',
													type: 'checkbox',
													required: true,
													checked: perms.includes('matchid'),
													value: 'matchid'
												},
												{
													name: 'Modification des tournois',
													type: 'checkbox',
													required: true,
													checked: perms.includes('edit_tournament'),
													value: 'edit_tournament'
												},
												{
													name: 'Modification du blog',
													type: 'checkbox',
													required: true,
													checked: perms.includes('edit_blog'),
													value: 'edit_blog'
												},
												{
													name: 'Modification des équipes',
													type: 'checkbox',
													required: true,
													checked: perms.includes('edit_team'),
													value: 'edit_team'
												},
												{
													name: 'Modifications des matchs',
													type: 'checkbox',
													required: true,
													checked: perms.includes('edit_match'),
													value: 'edit_match'
												},
												{
													name: 'Réaliser des prédictions',
													type: 'checkbox',
													required: true,
													checked: perms.includes('make_prediction'),
													value: 'make_prediction'
												},
												{
													name: 'Modifier les membres',
													type: 'checkbox',
													required: true,
													checked: perms.includes('edit_user'),
													value: 'edit_user'
												}
											],
											onSubmit: async (e) => {
												e.preventDefault();
												let new_perms = [];
												e.target
													.closest('form')
													.querySelectorAll('input[type="checkbox"]')
													.forEach((el) => {
														if (el.checked) {
															new_perms.push(el.value);
														}
													});

												const { data, error } = await supabase
													.from('perms')
													.update({ permissions: new_perms })
													.eq('user_id', id);
												if (error) {
													console.error(error);
													alert('An error occured while updating the user');
													return;
												} else {
													console.log(data);
												}

												new SucessModal({
													target: document.body,
													props: {
														message: 'Utilisateur modifié avec succès',
														onClose: () => {
															window.location.reload();
														}
													}
												});
											}
										}
									});
								}
							},
							{
								type: 'edit',
								title: "Renvoyer l'email de confirmation",
								handler: async () => {
									const adminSupa = await createAdminClient();
									const { data, error } = await adminSupa.auth.admin.getUserById(id);

									if (error) {
										console.error(error);
										alert('An error occured while resending the email');
										return;
									}

									const { error: error_ } = await supabase.auth.resend({
										type: 'signup',
										email: data.user.email
									});
									console.log(error_);
									window.location.reload();
								}
							}
						]
					}
				});
				e.stopPropagation();
			}
		}
	];

	onMount(async () => {
		const { data, error } = await supabase.rpc('get_service_key');
		if (error) {
			console.error(error);
		} else {
			service_key = data;
		}
	});
</script>

<h2 class="mb-4 text-4xl font-bold tracking-tight text-white">Utilisateurs</h2>
<div class="mt-2 bg-gray-800 rounded-lg sm:m-5">
	<Table {headers} {parseItems} {filters} {dbInfo} {actions} />
</div>

<style></style>
