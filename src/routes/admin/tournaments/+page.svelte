<script>
	import { supabase } from '$lib/supabaseClient';

	import Table from '$lib/components/admin/Table.svelte';
	import CrudForm from '$lib/components/modals/CrudForm.svelte';
	import { goto } from '$app/navigation';

	const headers = ['Nom', 'Période', 'Actions'];
	const type = 'Tournoi';

	async function handleSubmit(e) {
		e.preventDefault();
		const btn = e.target;
		btn.disabled = true;
		btn.textContent = 'En cours...';
		btn.classList.add('cursor-not-allowed', 'opacity-50');
		const form = e.target.closest('form');
		const data = new FormData(form);
		const payload = {};
		for (const [key, value] of data.entries()) {
			payload[key] = value;
		}
		payload['name_id'] = payload['title'].toLowerCase().replaceAll(' ', '');
		const { error } = await supabase.from('Tournaments').insert([payload]);
		if (error) {
			console.error(error);
			btn.disabled = false;
			btn.textContent = 'Erreur';
			btn.classList.toggle('cursor-not-allowed', 'opacity-50');
		} else {
			window.location.reload();
		}
	}

	function addNew() {
		new CrudForm({
			target: document.body,
			props: { fields, onSubmit: handleSubmit, type, type_accord: 'un' }
		});
	}

	let actions = [
		{
			type: 'view',
			handler: (e) => {
				e.preventDefault();
				goto(`/admin/tournaments/${e.target.closest('tr').firstChild.dataset.utils}`);
			}
		}
	];

	function parseItems(data) {
		let items = [];

		data?.forEach((element) => {
			let el = [
				{ value: element.title, data: element.slug },
				{ value: `${element.start} - ${element.end}` }
			];
			items = [...items, el];
		});
		return items;
	}

	const dbInfo = {
		table: 'Tournaments',
		key: '*'
	};
</script>

<section>
	<h1 class="text-3xl font-semibold text-white">Tournois</h1>
	<div class="mt-2 bg-gray-800 rounded-lg sm:m-5">
		<Table {headers} {parseItems} {addNew} {type} {actions} {dbInfo} searchable="title" />
	</div>
</section>

<style></style>
