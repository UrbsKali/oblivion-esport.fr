<script>
	import { supabase } from '$lib/supabaseClient';

	import CrudForm from '$lib/components/modals/CrudForm.svelte';
	import ReadModal from '$lib/components/modals/ReadModal.svelte';
	import Table from '$lib/components/admin/Table.svelte';

	const headers = ['Nom', 'TAG', 'Description', 'Actions'];
	const type = 'Teams';
	const type_accord = 'une';

	let actions = [
		{
			type: 'view',
			handler: async (e) => {
				e.preventDefault();
				let tr = e.target.closest('tr');
				let name = tr.children[0].innerText;
				const id = tr.children[0].dataset.utils;
				let tournament = tr.children[1].innerText;

				e.stopPropagation();
			}
		}
	];

	function parseItems(data) {
		let items = [];
		data?.forEach((element) => {
			const avatar = element.logo_url || '/v2/oblivion.png';
			let el = [
				{ value: element.name, data: element.id, avatar: avatar },
				{ value: element.tag },
				{ value: element.description || 'Aucune description' }
			];
			items = [...items, el];
		});
		return items;
	}
	const dbInfo = {
		table: 'Teams',
		key: 'id, name, description, logo_url, tag'
	};
</script>

<section>
	<h1 class="text-3xl font-semibold text-white sm:px-5">Toutes les équipes</h1>
	<div class="mt-2 bg-gray-800 rounded-lg sm:m-5">
		<Table {headers} {type} {type_accord} {parseItems} {dbInfo} {actions} />
	</div>
</section>

<style></style>
