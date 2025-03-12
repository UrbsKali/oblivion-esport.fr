<script>
	import Table from '$lib/components/admin/Table.svelte';
	import ReadModal from '$lib/components/modals/ReadModal.svelte';

	import { userdata } from '$lib/store';
	import { supabase } from '$lib/supabaseClient';
	import { page } from '$app/stores';
	import { onMount } from 'svelte';

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
					.select(
						'uid(id, username, avatar_url, tracker, other_providers(display_name, provider)), team_id, role, player_data'
					)
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
						},
						{
							label: 'Type',
							value: data.player_data?.type || 'Aucun type défini'
						},
						{
							label: 'Compte Discord',
							value:
								data.uid.other_providers?.find((el) => el.provider == 'discord')?.display_name ||
								'Pas de compte lié'
						},
						{
							label: 'Compte Epic Games',
							value:
								data.uid.other_providers?.find((el) => el.provider == 'epic')?.display_name ||
								'Pas de compte lié',
							href:
								data.uid.tracker ||
								`https://rocketleague.tracker.network/rocket-league/profile/epic/${data.uid.other_providers?.find((el) => el.provider == 'epic')?.display_name}/overview`
						},
						{
							label: 'Compte Riot',
							value:
								data.uid.other_providers?.find((el) => el.provider == 'riot')?.display_name ||
								'Pas de compte lié'
						}
					]
				};

				let actions = [];

				new ReadModal({
					target: document.body,
					props: {
						open: true,
						id: 'readModal_' + p_id,
						values,
						actions: actions
					}
				});
			}
		}
	];

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
			let avatar = el.uid.avatar_url || '/assets/oblivion.webp';
			let el_ = [
				{ value: el.uid.username, data: el.uid.id, avatar: avatar },
				{ value: el.role },
				{ value: is_valid ? 'Valide' : 'Informations manquantes' }
			];
			items.push(el_);
		}

		return items;
	}

	onMount(async () => {
		if (can_load) {
			const { data, error } = await supabase.from('Teams').select('name').eq('id', id).single();
			if (error) {
				console.error(error);
				return;
			}
			team = data;
		}
	});
</script>

<div class="flex flex-col items-center justify-center px-5 py-0 mx-auto sm:p-0">
	<div class="flex items-center justify-start w-full mb-5">
		<button
			class="flex items-center justify-center w-10 h-10 text-gray-200 bg-gray-800 border border-gray-700 rounded-full shadow hover:bg-opacity-10"
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
	</div>
	<div class="w-full mt-0 bg-gray-800 border border-gray-700 rounded-lg shadow backdrop-blur-sm">
		<Table
			{dbInfo}
			{parseItems}
			{headers}
			{filters}
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
