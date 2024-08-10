<script>
	// @ts-nocheck

	import Table from '$lib/components/Table.svelte';
	import { supabase } from '$lib/supabaseClient';
	import { onMount } from 'svelte';

	const headers = ['Nom', 'Période', 'Actions'];
	let items = [];
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

	let handleSubmit = async (e) => {
		e.preventDefault();
		const form = e.target.closest('form');
		const data = new FormData(form);
		const payload = {};
		for (const [key, value] of data.entries()) {
			payload[key] = value;
		}
		payload['name_id'] = payload['title'].toLowerCase().replaceAll(' ', '');
		console.log(payload);
		const { ret, error } = await supabase.from('Tournaments').insert([payload]);
		console.log(ret);
		if (error) {
			console.error(error);
		} else {
			let el = [{ value: payload.title }, { value: `${payload.start} - ${payload.end}` }];
			items = [...items, el];
			const modal = FlowbiteInstances.getInstance('Modal', 'CrudModal');
			modal.hide();
		}
	};

	let handleDelete = async (e) => {
		e.preventDefault();
		let tr = e.target.closest('tr');
		let name = tr.children[0].innerText;
		let [start, end] = tr.children[1].innerText.split(' - ');
		const response = await supabase
			.from('Tournaments')
			.delete()
			.eq('title', name)
			.eq('start', start)
			.eq('end', end);
		if (response.error) {
			console.error(response.error);
		} else {
			items = items.filter((el) => el[0] !== name);
		}
	};

	onMount(async () => {
		const { data, error } = await supabase.from('Tournaments').select();
		data?.forEach((element) => {
			let el = [{ value: element.title }, { value: `${element.start} - ${element.end}` }];
			items = [...items, el];
		});
	});
</script>

<section>
	<h1 class="text-3xl font-semibold text-gray-900 dark:text-white">Tournois</h1>
	<Table {headers} {items} {type} {fields} {handleSubmit} {handleDelete} />
</section>

<style></style>
