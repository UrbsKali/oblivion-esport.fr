<script>
	// @ts-nocheck

	import Table from '$lib/components/Table.svelte';
	import { supabase } from '$lib/supabaseClient';
	import { onMount } from 'svelte';

	const headers = ['Nom', 'Tournoi', 'Gagnant', 'Score', 'Actions'];
	let items = [];

	let teams_options = [];
	let t_id = 0;
	let selected_team = [];

	const type = 'Match';

	let editLoad = async (e) => {
		let tr = e.target.closest('tr');
		let id = tr.children[0].dataset.utils;
		const response = await supabase
			.from('Matchs')
			.select(
				'id, team_one(name, id), team_two(name, id), tournament_id(title, id), winner(name, id), date, score'
			)
			.eq('id', id);
		if (response.error) {
			console.error(response.error);
		} else {
			let data = response.data[0];
			fields[0].value = data.tournament_id.id;
			fields[0].data = data.id;
			await handleSelectUpdate({ target: { id: 'tournament_id', value: data.tournament_id.id } });
			fields[1].value = data.team_one.id;
			fields[2].value = data.team_two.id;
			await handleSelectUpdate({
				target: {
					id: 'team_one',
					value: data.team_one.id,
					selectedOptions: [{ innerText: data.team_one.name }]
				}
			});
			await handleSelectUpdate({
				target: {
					id: 'team_two',
					value: data.team_two.id,
					selectedOptions: [{ innerText: data.team_two.name }]
				}
			});

			let local_date = new Date(data.date).toLocaleString();
			fields[3].value = local_date.split(' ')[0].split('/').reverse().join('-');
			fields[4].value = local_date.split(' ')[1];
			fields[5].value = data.winner?.id;
			fields[6].value = data.score;

			const modal = FlowbiteInstances.getInstance('Modal', 'CrudModal');
			modal.show();
		}
	};

	let handleSubmit = async (e) => {
		e.preventDefault();
		const form = e.target.closest('form');
		const data = new FormData(form);
		const payload = {};
		for (const [key, value] of data.entries()) {
			if (key == 'winner' && value == 'NULL') continue;
			payload[key] = value;
		}
		payload.date += ` ${payload.time}+02`;
		delete payload.time;
		// parse score to always be in the form of '0-0' and the bigger number is the first
		if (payload.score) {
			let score = payload.score.replaceAll(' ', '').split('-');
			score = score.map((el) => parseInt(el));
			score = score.sort((a, b) => b - a);
			payload.score = `${score[0]}-${score[1]}`;
		}
		const { ret, error } = await supabase.from('Matchs').insert([payload]);
		if (error) {
			console.error(error);
		} else {
			const { data, error } = await supabase
				.from('Matchs')
				.select(
					`id, team_one(name, id), team_two(name, id), tournament_id(title, id), winner(name, id), date, score`
				)
				.eq('team_one', payload.team_one)
				.eq('team_two', payload.team_two)
				.eq('tournament_id', payload.tournament_id)
				.eq('date', payload.date);

			const element = data[0];
			let el = [
				{ value: `${element.team_one.name} vs ${element.team_two.name}`, data: element.id },
				{ value: element.tournament_id.title, data: element.tournament_id.id },
				{ value: element.winner?.name || '-', data: element.winner?.id || '' },
				{ value: element.score || '-' }
			];
			items = [...items, el];
			const modal = FlowbiteInstances.getInstance('Modal', 'CrudModal');
			modal.hide();
		}
	};

	let handleEdit = async (e) => {
		e.preventDefault();
		const form = e.target.closest('form');
		console.log(form);
		let id = form.firstChild.firstChild.firstChild.dataset.utils;
		const data = new FormData(form);
		const payload = {};
		for (const [key, value] of data.entries()) {
			if (key == 'winner' && value == 'NULL') continue;
			payload[key] = value;
		}
		payload.date += ` ${payload.time}+02`;
		delete payload.time;
		console.log(payload);
		const { ret, error } = await supabase.from('Matchs').update([payload]).eq('id', id);
		if (error) {
			console.error(error);
		} else {
			let el = [
				{
					value: `${document.querySelector('select#team_one').selectedOptions[0].innerText} vs ${document.querySelector('select#team_two').selectedOptions[0].innerText}`,
					data: id
				},
				{
					value: document.querySelector('select#tournament_id').selectedOptions[0].innerText,
					data: payload.tournament_id
				},
				{
					value: document.querySelector('select#winner').selectedOptions[0]?.innerText || '-',
					data: payload.winner || ''
				},
				{ value: payload.score || '-' }
			];
			items = [...items.filter((el) => el[0].data != id), el];

			const modal = FlowbiteInstances.getInstance('Modal', 'CrudModal');
			modal.hide();
		}
	};

	let handleDelete = async (e) => {
		e.preventDefault();
		let tr = e.target.closest('tr');
		let id = tr.children[0].dataset.utils;
		const response = await supabase.from('Matchs').delete().eq('id', id);
		if (response.error) {
			console.error(response.error);
		} else {
			items = items.filter((el) => el[0].data != id);
		}
	};

	let handleSelectUpdate = async (e) => {
		// fetch teams options for the select field
		if (e.target.id == 'tournament_id') {
			t_id = e.target.value;
			fields[1].options = teams_options.filter((el) => el.data == t_id);
			fields[2].options = teams_options.filter((el) => el.data == t_id);
		}
		if (e.target.id == 'team_one') {
			fields[5].options[0] = { name: e.target.selectedOptions[0].innerText, value: e.target.value };
			fields[2].options = teams_options.filter((el) => el.data == t_id);
			fields[2].options = fields[2].options.filter((el) => el.value != e.target.value);
		}
		if (e.target.id == 'team_two') {
			fields[5].options[1] = { name: e.target.selectedOptions[0].innerText, value: e.target.value };
		}
	};

	let actions = [
		{
			title: 'Editer',
			type: 'edit',
			handler: editLoad
		},
		{
			title: 'Supprimer',
			type: 'delete',
			handler: handleDelete
		}
	];

	let fields = [
		{
			name: 'Tournoi',
			id: 'tournament_id',
			type: 'select',
			required: true,
			options: [],
			wide: true,
			onChange: handleSelectUpdate
		},
		{
			name: 'Equipe 1',
			id: 'team_one',
			type: 'select',
			required: true,
			options: [...teams_options],
			onChange: handleSelectUpdate
		},
		{
			name: 'Equipe 2',
			id: 'team_two',
			type: 'select',
			required: true,
			options: [...teams_options],
			onChange: handleSelectUpdate
		},
		{
			name: 'Date',
			id: 'date',
			type: 'date',
			required: true
		},
		{
			name: 'Heure',
			id: 'time',
			type: 'time',
			required: true
		},
		{
			name: 'Gagnant',
			id: 'winner',
			type: 'select',
			required: false,
			options: []
		},
		{
			name: 'Score',
			id: 'score',
			type: 'text',
			required: false,
			placeholder: '0-0'
		}
	];

	onMount(async () => {
		{
			// fetch tournament options for the select field
			const { data, error } = await supabase.from('Tournaments').select();
			data?.forEach((element) => {
				let el = { name: element.title, value: element.id };
				fields[0].options = [...fields[0].options, el];
			});
		}
		{
			// fetch teams options for the select field
			const { data, error } = await supabase.from('Teams').select();
			data?.forEach((element) => {
				teams_options = [
					...teams_options,
					{ name: element.name, value: element.id, data: element.tournament_id }
				];
			});
			fields[1].options = teams_options;
			fields[2].options = teams_options;
		}
	});

	const filters = [
		{
			category: 'Tournoi',
			value: 'tournament_id.id',
			options: [
				{ name: 'TWC 5', value: 8 },
				{ name: 'EWC 1', value: 13 }
			]
		}
	];
	function parseItems(data) {
		let items = [];
		data?.forEach((element) => {
			let el = [
				{ value: `${element.team_one.name} vs ${element.team_two.name}`, data: element.id },
				{ value: element.tournament_id.title, data: element.tournament_id.id },
				{ value: element.winner?.name || '-', data: element.winner?.id || '' },
				{ value: element.score || '-' }
			];
			items = [...items, el];
		});
		return items;
	}
	const dbInfo = {
		table: 'Matchs',
		key: 'id, team_one(name, id), team_two(name, id), tournament_id!inner(title, id), winner(name, id), date, score'
	};
</script>

<section>
	<h1 class="text-3xl font-semibold text-gray-900 dark:text-white">Matchs</h1>
	<Table {headers} {items} {type} {actions} {parseItems} {dbInfo} {filters} />
</section>

<style></style>
