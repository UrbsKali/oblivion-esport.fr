<script>
	import Table from '$lib/components/admin/Table.svelte';
	import CrudForm from '$lib/components/modals/CrudForm.svelte';
	import SucessModal from '$lib/components/modals/InfoModal.svelte';
	import ReadModal from '$lib/components/modals/ReadModal.svelte';

	import { userdata } from '$lib/store';
	import { supabase } from '$lib/supabaseClient';
	import { page } from '$app/stores';
	import { onMount } from 'svelte';
	import { goto } from '$app/navigation';

	let user;
	let id;
	let filters = [];
	let can_load = false;

	let team = {};

	page.subscribe((value) => {
		if (value) {
			id = value.params.id;
			can_load = true;
			filters = [
				{
					category: 'hidden',
					value: 'team_id.id',
					options: [{ value: id, active: true }]
				}
			];
		}
	});

	userdata.subscribe(async (value) => {
		if (value) {
			user = value;

			// add team role to user data
			const { data: data_, error: error_ } = await supabase
				.from('member_of')
				.select('role')
				.eq('uid', user.id)
				.eq('team_id', id)
				.single();
			if (error_) {
				console.error(error_);
				return;
			}
			user.team = { role: data_.role };
		}
	});

	const dbInfo = {
		table: 'member_of',
		key: 'uid(id, username, avatar_url), team_id!inner(id), role'
	};

	const headers = ['Nom', 'Role', 'Profil', 'Actions'];

	let actions = [
		{
			type: 'view',
			handler: async (e) => {
				e.preventDefault();
				const p_id = e.target.closest('tr').firstChild.dataset.utils;
				console.log(p_id);
				const { data, error } = await supabase
					.from('member_of')
					.select('uid(id, username, avatar_url), team_id, role ')
					.eq('uid', p_id)
					.eq('team_id', id)
					.single();
				if (error) {
					console.error(error);
					return;
				}

				const values = {
					header: {
						title: `${data.uid.username}`
					},
					body: [
						{
							label: 'Role',
							value: data.role || ''
						}
					]
				};

				let actions = [];

				if ((user.id === data.uid.id || user.team.role === 'owner') && data.role !== 'owner') {
					actions.push({
						type: 'delete',
						title: 'Supprimer',
						handler: handleRemovePlayer
					});
				}

				new ReadModal({
					target: document.body,
					props: {
						open: true,
						id: 'readModal-' + p_id,
						values,
						actions: actions
					}
				});
			}
		}
	];

	async function handleRemovePlayer(e) {
		e.preventDefault();
		const id = e.target.closest('.modal').id.split('-')[1];
		const { data, error } = await supabase.from('member_of').delete().eq('id', id);
		if (error) {
			console.error(error);
			alert("Une erreur est survenue lors de la suppression du membre de l'équipe");
			return;
		}
		document.getElementById('readModal-' + id).remove();
	}

	async function parseItems(data) {
		let items = [];
		for (let i = 0; i < data.length; i++) {
			const { data: is_valid, error: error_ } = await supabase.rpc('check_user', {
				u_id: data[i].uid.id
			});
			if (error_) {
				console.error(error_);
				return;
			}
			let el = data[i];
			let avatar = el.uid.avatar_url || '/v2/oblivion.png';
			let el_ = [
				{ value: el.uid.username, data: el.uid.id, avatar: avatar },
				{ value: el.role },
				{ value: is_valid ? 'Valide' : 'Informations manquantes' }
			];
			items.push(el_);
		}

		return items;
	}

	async function addNew() {
		new CrudForm({
			target: document.body,
			props: {
				fields: [
					{
						type: 'info',
						text: "Ajouter un membre à l'équipe",
						wide: true
					},
					{
						name: 'Membre',
						id: 'member',
						type: 'autocomplete',
						value: '',
						data: '',
						required: true,
						wide: true,
						onChange: async (e) => {
							// search through users
							const { data, error } = await supabase
								.from('profiles')
								.select('id, username, avatar_url')
								.ilike('username', `${e.target.value}*`)
								.range(0, 4);
							if (error) {
								console.error(error);
								return;
							}
							// create options
							let options = [];
							for (let i = 0; i < data.length; i++) {
								let el = data[i];
								let avatar = el.avatar_url || '/v2/oblivion.png';
								options.push({ value: el.id, text: el.username, image: avatar });
							}
							return options;
						}
					},
					{
						name: 'Rôle',
						type: 'select',
						id: 'role',
						required: true,
						options: [
							{ value: 'player', text: 'Joueur' },
							{ value: 'substitute', text: 'Remplaçant' }
						],
						wide: true
					}
				],
				type_accord: 'un',
				type: 'joueur',
				onSubmit: async (e) => {
					// get forms data
					e.preventDefault();
					const form_data = new FormData(e.target.closest('form'));
					let data = {};
					for (let [key, value] of form_data.entries()) {
						if (key.startsWith('member')) {
							const uid = document.querySelector('label[for="member"]').dataset.utils;
							let role_slug = 'role';
							data.member = { uid: uid, role: form_data.get(role_slug) };
						} else if (key.startsWith('role')) {
							continue;
						} else {
							data[key.toLowerCase()] = value;
						}
					}

					// add user to team
					console.log(data.member);
					const { data: data___, error: error__ } = await supabase
						.from('member_of')
						.insert({ team_id: id, uid: data.member.uid, role: data.member.role });
					if (error__) {
						console.error(error__);
						alert("Une erreur est survenue lors de l'ajout du membre à l'équipe");
						return;
					}
					new SucessModal({
						target: document.body,
						props: {
							message: 'Le joueur a bien été ajouté'
						}
					});
				}
			}
		});
	}

	onMount(async () => {
		if (can_load) {
			const { data, error } = await supabase.from('Teams').select('name').eq('id', id).single();
			if (error) {
				console.error(error);
				return;
			}
			document.title = `Oblivion Esport - ${data.name} Team`;
			team = data;
		}
	});
</script>

<div class="flex flex-col items-center justify-center px-5 py-0 mx-auto sm:p-0">
	<div class="flex items-center justify-start w-full mb-5 sm:w-9/12 md:w-6/12">
		<button
			class="flex items-center justify-center w-10 h-10 text-gray-200 bg-gray-900 bg-opacity-0 border border-gray-700 rounded-full shadow hover:bg-opacity-10"
			on:click={() => window.history.back()}
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
		<h2 class="text-2xl font-bold text-center text-gray-200" id="teamName">{team.name}</h2>
		{#if user?.team?.role === 'owner'}
			<button
				class="flex items-center justify-center w-10 h-10 text-gray-200 bg-gray-900 bg-opacity-0 border border-gray-700 rounded-full shadow hover:bg-opacity-10"
				on:click={async (e) => {
					// delete team
					const { data, error } = await supabase.from('Teams').delete().eq('id', id);
					if (error) {
						console.error(error);
						alert("Une erreur est survenue lors de la suppression de l'équipe");
						return;
					}
					goto('/v2/user/teams', { replaceState: true });
				}}
			>
				<!--Trash icon-->
				<svg
					class="w-5 h-5 dark:text-white hover:text-red-500"
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
		{/if}
	</div>
	<div
		class="w-full mt-0 bg-gray-900 bg-opacity-0 border border-gray-700 rounded-lg shadow sm:w-9/12 backdrop-blur-sm md:w-6/12"
	>
		<Table
			{dbInfo}
			{parseItems}
			{headers}
			{filters}
			{addNew}
			{actions}
			{can_load}
			type="joueur"
			type_accord="un"
		/>
	</div>
</div>

<style>
	#teamName {
		width: calc(100% - 80px);
	}
</style>
