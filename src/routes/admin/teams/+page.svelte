<script>
	import { supabase } from '$lib/supabaseClient';

	import CrudForm from '$lib/components/modals/CrudForm.svelte';
	import ReadModal from '$lib/components/modals/ReadModal.svelte';
	import Table from '$lib/components/admin/Table.svelte';

	const headers = ['Nom', 'Tournoi', 'Actions'];
	let items = [];
	const type = 'Teams';
	const type_accord = 'une';
	let fields = [
		{
			name: 'Nom',
			id: 'name',
			type: 'text',
			required: true,
			placeholder: "Nom de l'équipe",
			wide: true
		},
		{
			name: 'Tournoi',
			id: 'tournament_id',
			type: 'select',
			required: true,
			options: [],
			wide: true
		}
	];

	let handleSubmit = async (e) => {
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
		const { error } = await supabase.from('Teams').insert([payload]);
		if (error) {
			console.error(error);
			btn.disabled = false;
			btn.textContent = 'Erreur';
			btn.classList.remove('cursor-not-allowed', 'opacity-50');
		} else {
			window.location.reload();
		}
	};

	let handleDelete = async (e) => {
		e.preventDefault();
		const btn = e.target;
		btn.disabled = true;
		btn.textContent = 'En cours...';
		btn.classList.add('cursor-not-allowed', 'opacity-50');
		const id = e.target.closest('.popup').id.split('-')[1];
		const response = await supabase.from('Teams').delete().eq('id', id);
		if (response.error) {
			console.error(response.error);
			btn.disabled = false;
			btn.textContent = 'Erreur';
			btn.classList.remove('cursor-not-allowed', 'opacity-50');
		} else {
			window.location.reload();
		}
	};
	let actions = [
		{
			type: 'view',
			handler: async (e) => {
				e.preventDefault();
				let tr = e.target.closest('tr');
				let name = tr.children[0].innerText;
				const id = tr.children[0].dataset.utils;
				let tournament = tr.children[1].innerText;
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
									label: 'Tournoi',
									value: tournament
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

	async function addNew() {
		const { data, error } = await supabase.from('Tournaments').select();
		data?.forEach((element) => {
			let el = { text: element.title, value: element.id };
			fields[1].options = [...fields[1].options, el];
		});
		new CrudForm({
			target: document.body,
			props: { fields, onSubmit: handleSubmit, type, type_accord: 'une', open: true }
		});
	}

	function parseItems(data) {
		let items = [];
		data?.forEach((element) => {
			let el = [
				{ value: element.name, data: element.id },
				{ value: element.tournament_id.title, data: element.tournament_id.id }
			];
			items = [...items, el];
		});
		return items;
	}
	const dbInfo = {
		table: 'Teams',
		key: 'id, name, tournament_id(title, id)'
	};
</script>

<section>
	<h1 class="text-3xl font-semibold text-gray-900 dark:text-white">Teams</h1>
	<Table {headers} {type} {type_accord} {parseItems} {dbInfo} {actions} {addNew} />
</section>

<style></style>
