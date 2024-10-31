<script>
	import { onMount } from 'svelte';
	import { supabase, supabaseUrl } from '$lib/supabaseClient';
	import { createClient } from '@supabase/supabase-js';

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
			const avatar = el.avatar_url || '/v2/oblivion.png';
			items.push([
				{ value: el.username, data: el.id, avatar: avatar },
				{ value: el.perms.permissions }
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

				let perms = tr.children[1].innerText;

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
									value: perms
								}
							]
						},
						actions: [
							{
								type: 'validate',
								title: 'Modifier',
								handler: () => {
									new CrudForm({
										target: document.body,
										props: {
											title: "Modifier l'utilisateur",
											fields: [
												{
													name: 'Rôle',
													id: 'role',
													type: 'select',
													options: [
														{ value: 'panel_admin', text: 'Accès au panel admin' },
														{ value: 'register', text: 'Accès au inscription' },
														{ value: 'matchid', text: 'Accès au MatchID' },
														{ value: 'edit_tournament', text: 'Modification des tournois' },
														{ value: 'edit_blog', text: 'Modification du blog' },
														{ value: 'edit_team', text: 'Modification des équipes' },
														{ value: 'edit_match', text: 'Modifications des matchs' },
														{ value: 'make_prediction', text: 'Réaliser des prédictions' },
														{ value: 'edit_user', text: 'Modifier les membres' }
													],
													required: true
												}
											],
											onSubmit: async (e) => {
												e.preventDefault();
												const role = document.querySelector('#role').value;
												if (role === '') {
													alert('No role provided');
													return;
												}

												// append the role to the user current roles
												let new_perms = [];
												if (perms != 'null') {
													new_perms = [...perms, role];
												} else {
													new_perms = [role];
												}

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
