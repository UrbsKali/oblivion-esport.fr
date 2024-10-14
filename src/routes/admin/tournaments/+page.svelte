<script>
	import { supabase } from '$lib/supabaseClient';

	import Table from '$lib/components/admin/Table.svelte';
	import ReadModal from '$lib/components/modals/ReadModal.svelte';
	import CrudForm from '$lib/components/modals/CrudForm.svelte';

	const headers = ['Nom', 'Période', 'Actions'];
	const type = 'Tournoi';
	const fields = [
		{
			name: 'Nom',
			id: 'title',
			type: 'text',
			required: true,
			placeholder: 'Nom du tournoi',
			wide: true
		},
		{
			name: 'Début',
			id: 'start',
			type: 'date',
			required: true
		},
		{
			name: 'Fin',
			id: 'end',
			type: 'date',
			required: true
		}
	];

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
			props: { fields, onSubmit: handleSubmit, type, type_accord: 'un', open: true }
		});
	}

	async function handleDelete(e) {
		e.preventDefault();
		const btn = e.target;
		btn.disabled = true;
		btn.textContent = 'En cours...';
		btn.classList.add('cursor-not-allowed', 'opacity-50');
		const id = e.target.closest('.popup').id.split('-')[1];
		const response = await supabase.from('Tournaments').delete().eq('id', id);
		if (response.error) {
			console.error(response.error);
			btn.disabled = false;
			btn.textContent = 'Erreur';
			btn.classList.remove('cursor-not-allowed', 'opacity-50');
		} else {
			window.location.reload();
		}
	}

	let actions = [
		{
			type: 'view',
			handler: (e) => {
				e.preventDefault();
				let tr = e.target.closest('tr');
				let name = tr.children[0].innerText;
				const id = tr.children[0].dataset.utils;
				let [start, end] = tr.children[1].innerText.split(' - ');
				new ReadModal({
					target: document.body,
					props: {
						open: true,
						id: id,
						values: {
							header: {
								title: name
							},
							body: [
								{
									label: 'Nom',
									value: name
								},
								{
									label: 'Période',
									value: `${start} - ${end}`
								}
							]
						},
						actions: [
							{
								type: 'delete',
								title: 'Supprimer',
								handler: handleDelete
							}
						]
					}
				});
				e.stopPropagation();
			}
		}
	];

	function parseItems(data) {
		let items = [];

		data?.forEach((element) => {
			let el = [
				{ value: element.title, data: element.id },
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
	<h1 class="text-3xl font-semibold text-gray-900 dark:text-white">Tournois</h1>
	<Table {headers} {parseItems} {addNew} {type} {actions} {dbInfo} />
</section>

<style></style>
