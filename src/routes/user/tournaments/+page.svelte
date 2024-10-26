<script>
	import Table from '$lib/components/admin/Table.svelte';
	import CrudForm from '$lib/components/modals/CrudForm.svelte';
	import SucessModal from '$lib/components/modals/SucessModal.svelte';
	import { userdata } from '$lib/store';
	import { supabase } from '$lib/supabaseClient';

	let user;

	let filters = [];

	userdata.subscribe((value) => {
		if (value) {
			user = value;
			filters = [
				{
					category: 'hidden',
					value: 'part_of.team_id.member_of.uid',
					options: [{ value: user.id, active: true }]
				}
			];
		}
	});

	const statusText = {
		pending: 'En attente',
		accepted: 'Accepté',
		refused: 'Refusé'
	};

	const dbInfo = {
		table: 'Tournaments',
		key: 'id, title, part_of!inner(status, team_id!inner(id, name, logo_url, tag, member_of!inner(uid)))), logo_url'
	};

	const headers = ['Tournoi', 'équipe', 'Status', 'Actions'];

	async function parseItems(data) {
		let items = [];
		console.log(data);
		for (let i = 0; i < data.length; i++) {
			let el = data[i];
			let el_ = [
				{ value: el.title, data: el.id },
				{ value: el.part_of[0].team_id.name, avatar: el.part_of[0].team_id.logo_url },
				{ value: statusText[el.part_of[0].status] }
			];
			items.push(el_);
		}

		return items;
	}
</script>

<div class="flex flex-col items-center justify-center px-5 py-0 mx-auto sm:p-0">
	<div
		class="w-full mt-0 bg-gray-900 bg-opacity-0 border border-gray-700 rounded-lg shadow sm:w-9/12 backdrop-blur-sm md:w-6/12"
	>
		<Table {dbInfo} {parseItems} {headers} {filters} type="Tournoi" type_accord="un" />
	</div>
</div>
