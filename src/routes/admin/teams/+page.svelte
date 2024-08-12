<script>
	// @ts-nocheck

	import Table from '$lib/components/Table.svelte';
	import { supabase } from '$lib/supabaseClient';
	import { onMount } from 'svelte';

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
		const form = e.target.closest('form');
		const data = new FormData(form);
		const payload = {};
		for (const [key, value] of data.entries()) {
			payload[key] = value;
		}
		const { ret, error } = await supabase.from('Teams').insert([payload]);
		if (error) {
			console.error(error);
		} else {
			let el = [
				{ value: payload.name },
				{
					value: form.querySelector('select').selectedOptions[0].innerText,
					data: payload.tournament_id
				}
			];
			items = [...items, el];
			const modal = FlowbiteInstances.getInstance('Modal', 'CrudModal');
			modal.hide();
		}
	};

	let handleDelete = async (e) => {
		e.preventDefault();
		let tr = e.target.closest('tr');
		let name = tr.children[0].innerText;
		let id = tr.children[1].dataset.utils;
		const response = await supabase.from('Teams').delete().eq('name', name).eq('tournament_id', id);
		if (response.error) {
			console.error(response.error);
		} else {
			items = items.filter((el) => el[0].value !== name);
		}
	};

	onMount(async () => {
		const { data, error } = await supabase.from('Tournaments').select();
		data?.forEach((element) => {
			let el = { name: element.title, value: element.id };
			fields[1].options = [...fields[1].options, el];
		});
		{
			const { data, error } = await supabase.from('Teams').select(`name, tournament_id(title, id)`);
			data?.forEach((element) => {
				let el = [
					{ value: element.name },
					{ value: element.tournament_id.title, data: element.tournament_id.id }
				];
				items = [...items, el];
			});
		}
	});
</script>

<section>
	<h1 class="text-3xl font-semibold text-gray-900 dark:text-white">Teams</h1>
	<Table {headers} {items} {type} {type_accord} {fields} {handleSubmit} {handleDelete} />
</section>

<style></style>
