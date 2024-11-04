<script>
	import Table from '$lib/components/admin/Table.svelte';
	import { goto } from '$app/navigation';
	import { formatText } from '$lib/utils';

	const headers = ['Nom', 'TAG', 'Description', 'Actions'];
	const type = 'Teams';
	const type_accord = 'une';

	let actions = [
		{
			type: 'view',
			handler: async (e) => {
				e.preventDefault();
				const tr = e.target.closest('tr');
				const id = tr.children[0].dataset.utils;
				goto(`/admin/teams/${id}`);
				e.stopPropagation();
			}
		}
	];

	function parseItems(data) {
		let items = [];
		data?.forEach((element) => {
			const avatar = element.logo_url || '/assets/oblivion.webp';
			let el = [
				{ value: element.name, data: element.id, avatar: avatar },
				{ value: element.tag },
				{ value: formatText(element.description) || 'Aucune description' }
			];
			console.log(el);
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
