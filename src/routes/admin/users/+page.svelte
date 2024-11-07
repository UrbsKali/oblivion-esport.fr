<script>
	import { onMount } from 'svelte';
	import { supabase, supabaseUrl } from '$lib/supabaseClient';
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

				let perms = tr.children[1].innerText.split(',');

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
							}
						]
					}
				});
				e.stopPropagation();
			}
		}
	];

	async function addNew() {
		new CrudForm({
			target: document.body,
			props: {
				open: true,
				fields: [
					{
						name: 'Nom',
						type: 'text',
						placeholder: 'Rob, aka Robert',
						required: true,
						wide: true
					},
					{
						name: 'Rôle',
						id: 'role',
						type: 'select',
						options: [
							{ value: 'admin', text: 'Admin' },
							{ value: 'bureau', text: 'Bureau' },
							{ value: 'cdp', text: 'Chef de projet' },
							{ value: 'membre', text: 'Membre' }
						],
						required: true
					},
					{
						name: 'Projet',
						id: 'project',
						type: 'select',
						options: [
							{ value: '1', text: 'CDR' },
							{ value: '2', text: 'Travelers' },
							{ value: '3', text: 'Exaudus' },
							{ value: '8', text: 'Bureau' }
						],
						required: true
					},
					{
						name: 'email',
						type: 'email',
						placeholder: 'davincibot@devinci.fr',
						required: true,
						wide: true
					}
				],
				onSubmit: async (e) => {
					e.preventDefault();
					if (service_key === '') {
						console.error('No service key');
						alert(
							'You do not have the required permissions to perform this action (required: admin)'
						);
						return;
					}

					const form = {
						email: document.querySelector('#email').value,
						role: document.querySelector('#role').value,
						username: document.querySelector('#nom').value,
						project: document.querySelector('#project').value
					};

					if (!form.email) {
						alert('No email provided');
						return;
					}
					if (!form.role) {
						alert('No role provided');
						return;
					}
					if (!form.username) {
						alert('No username provided');
						return;
					}

					{
						// create admin client
						const admin_supabase = createClient(supabaseUrl, service_key, {
							auth: {
								autoRefreshToken: false,
								persistSession: false
							}
						});

						// invite the user with the email
						const { data, error } = await admin_supabase.auth.admin.inviteUserByEmail(form.email);
						if (error) {
							console.error(error);
							alert('An error occured while inviting the user');
							return;
						} else {
							console.log('User invited');
							form.id = data.user.id;
						}
					}

					console.log(form);

					{
						const { data, error } = await supabase.from('profiles').insert({
							id: form.id,
							username: form.username,
							role: form.role
						});
						if (error) {
							console.error(error);
							alert('An error occured while updating the user');
							return;
						} else {
							console.log(data);
						}
					}

					{
						const { data, error } = await supabase.from('member_of').insert({
							profile: form.id,
							project: form.project
						});
						if (error) {
							console.error(error);
							alert('An error occured while adding the user to the project');
							return;
						} else {
							console.log(data);
						}
					}

					new SucessModal({
						target: document.body,
						props: {
							message: 'Utilisateur ajouté avec succès',
							onClose: () => {
								window.location.reload();
							}
						}
					});
				}
			}
		});
	}

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
