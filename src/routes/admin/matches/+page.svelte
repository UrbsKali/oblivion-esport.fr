<script>
	// @ts-nocheck

	import Table from '$lib/components/admin/Table.svelte';
	import CrudForm from '$lib/components/modals/CrudForm.svelte';
	import ReadModal from '$lib/components/modals/ReadModal.svelte';
	import { supabase } from '$lib/supabaseClient';
	import { onMount } from 'svelte';

	const headers = ['Nom', 'Tournoi', 'Gagnant', 'Score', 'Actions'];
	let items = [];

	let teams_options = [];
	let t_id = 0;
	let selected_team = [];

	const type = 'Match';

	const PHASE_RE = /^(?:bracket|backet)-(w|l)-p(\d+)-g(\d+)$/i;

	let editLoad = async (e) => {
		const id = e.target.closest('.modal').id.split('-')[1];
		const { data, error } = await supabase
			.from('Matchs')
			.select(
				'id, team_one(name, id), team_two(name, id), tournament_id(title, id), winner(name, id), date, score, phase'
			)
			.eq('id', id)
			.single();
		if (error) {
			console.error(error);
			alert('Une erreur est survenue lors de la récupération des données');
			return;
		} else {
			fields[0].value = data.tournament_id.id;
			fields[0].data = data.id;
			fields[0].options = [
				{ text: data.tournament_id.title, value: data.tournament_id.id, autoselect: true }
			];
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

			// Phase parsing for bracket form fields
			let side = 'none';
			let p = '';
			let g = '';
			const m = data?.phase ? String(data.phase).match(PHASE_RE) : null;
			if (m) {
				side = m[1].toLowerCase();
				p = m[2];
				g = m[3];
			}
			fields[7].value = side; // bracket_side
			fields[8].value = p; // phase_no
			fields[9].value = g; // group_no

			new CrudForm({
				target: document.body,
				props: {
					id: id,
					type: 'Match',
					type_accord: 'le',
					action: 'Modifier',
					fields: fields,
					onSubmit: handleEdit
				}
			});
		}
	};

	async function handleSubmit(e) {
		e.preventDefault();
		const form = e.target.closest('form');
		const data = new FormData(form);
		const payload = {};
		for (const [key, value] of data.entries()) {
			if (key == 'winner' && value == 'NULL') continue;
			payload[key] = value;
		}
		payload.date += ` ${payload.time}+01`;
		delete payload.time;
		// parse score to always be in the form of '0-0' and the bigger number is the first
		if (payload.score) {
			let score = payload.score.replaceAll(' ', '').split('-');
			score = score.map((el) => parseInt(el));
			score = score.sort((a, b) => b - a);
			payload.score = `${score[0]}-${score[1]}`;
		}
		// construct phase from bracket-side/phase/group
		const side = payload.bracket_side;
		const p = payload.phase_no;
		const g = payload.group_no;
		if (side && side !== 'none' && p && g) {
			payload.phase = `bracket-${side}-p${p}-g${g}`;
		} else {
			payload.phase = 'group';
		}
		delete payload.bracket_side;
		delete payload.phase_no;
		delete payload.group_no;

		const { ret, error } = await supabase.from('Matchs').insert([payload]);
		if (error) {
			console.error(error);
			alert("Une erreur est survenue lors de l'ajout du match");
		} else {
			window.location.reload();
		}
	}

	async function addNew() {
		new CrudForm({
			target: document.body,
			props: {
				type: 'Match',
				type_accord: 'un',
				action: 'Ajouter',
				fields: fields,
				changeArgs: 2,
				onSubmit: handleSubmit
			}
		});
	}

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
		payload.date += ` ${payload.time}+01`;
		delete payload.time;
		// update phase
		const side = payload.bracket_side;
		const p = payload.phase_no;
		const g = payload.group_no;
		if (side && side !== 'none' && p && g) {
			payload.phase = `bracket-${side}-p${p}-g${g}`;
		} else {
			payload.phase = 'group';
		}
		delete payload.bracket_side;
		delete payload.phase_no;
		delete payload.group_no;
		console.log(payload);
		const { ret, error } = await supabase.from('Matchs').update([payload]).eq('id', id);
		if (error) {
			console.error(error);
		} else {
			window.location.reload();
		}
	};

	let handleDelete = async (e) => {
		e.preventDefault();
		const id = e.target.closest('.modal').id.split('-')[1];
		const response = await supabase.from('Matchs').delete().eq('id', id);
		if (response.error) {
			console.error(response.error);
		} else {
			window.location.reload();
		}
	};

	let handleSelectUpdate = async (e, c_fields) => {
		if (!c_fields) {
			c_fields = fields;
			console.log('parent field');
		}
		// fetch teams options for the select field
		if (e.target.id == 'tournament_id') {
			console.log(e.target.value);
			t_id = e.target.value;
			c_fields[1].options = teams_options.filter((el) => el.data == t_id);
			c_fields[2].options = teams_options.filter((el) => el.data == t_id);
			c_fields[5].options = [{ text: 'Aucun', value: 'NULL' }];
		}
		if (e.target.id == 'team_one') {
			c_fields[5].options[0] = {
				text: e.target.selectedOptions[0].innerText,
				value: e.target.value
			};
			c_fields[2].options = teams_options.filter((el) => el.data == t_id);
			c_fields[2].options = c_fields[2].options.filter((el) => el.value != e.target.value);
		}
		if (e.target.id == 'team_two') {
			c_fields[5].options[1] = {
				text: e.target.selectedOptions[0].innerText,
				value: e.target.value
			};
		}
		return c_fields;
	};

	let actions = [
		{
			type: 'view',
			handler: async (e) => {
				e.stopPropagation();
				const id = e.target.closest('tr').children[0].dataset.utils;
				const { data, error } = await supabase
					.from('Matchs')
					.select(
						'id, team_one(name, id), team_two(name, id), tournament_id(title, id), winner(name, id), date, score, phase'
					)
					.eq('id', id)
					.single();
				if (error) {
					console.error(error);
					alert('Une erreur est survenue lors de la récupération des données');
					return;
				}
				new ReadModal({
					target: document.body,
					props: {
						id,
						values: {
							header: {
								title: `${data.team_one.name} vs ${data.team_two.name}`,
								sub: data.date.split('T')[0] + ' ' + data.date.split('T')[1].split('+')[0]
							},
							body: [
								{ label: 'Tournoi', value: data.tournament_id.title },
								{ label: 'Gagnant', value: data.winner?.name || '-' },
								{ label: 'Score', value: data.score || '-' }
							]
						},
						actions: [
							{
								type: 'edit',
								title: 'Modifier',
								handler: editLoad
							},
							{
								type: 'delete',
								title: 'Supprimer',
								handler: handleDelete
							}
						]
					}
				});
			}
		}
	];

	let fields = [
		{
			name: 'Tournoi',
			id: 'tournament_id',
			type: 'select',
			required: true,
			options: [{ text: 'Chargement...', value: 0 }],
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
		},
		// New bracket fields
		{
			name: 'Type de phase',
			id: 'bracket_side',
			type: 'select',
			required: false,
			options: [
				{ text: 'Phase de groupe', value: 'none' },
				{ text: 'Winner bracket', value: 'w' },
				{ text: 'Loser bracket', value: 'l' }
			]
		},
		{
			name: 'Numéro de phase (p)',
			id: 'phase_no',
			type: 'number',
			required: false,
			placeholder: '1',
			min: 1
		},
		{
			name: 'Numéro de match (g)',
			id: 'group_no',
			type: 'number',
			required: false,
			placeholder: '1',
			min: 1
		}
	];

	const filters = [
		{
			category: 'Tournoi',
			value: 'tournament_id.id',
			options: [
				{ name: 'TWC 5', value: 8 },
				{ name: 'EWC 1', value: 13 },
				{ name: 'TWC 6', value: 22 }
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

	onMount(async () => {
		const { data, error } = await supabase
			.from('Teams')
			.select('name, id, part_of!inner(tournament_id)')
			.eq('part_of.status', 'accepted')
			.order('name');
		if (error) {
			console.error(error);
			alert('Une erreur est survenue lors de la récupération des données');
			return;
		}
		teams_options = data.map((el) => {
			return { text: el.name, value: el.id, data: el.part_of[0].tournament_id };
		});
		console.log(teams_options);
		const { data: tournaments, error: error_t } = await supabase
			.from('Tournaments')
			.select('title, id');
		if (error_t) {
			console.error(error_t);
			alert('Une erreur est survenue lors de la récupération des données');
			return;
		}
		fields[0].options = tournaments.map((el) => {
			return { text: el.title, value: el.id };
		});
	});
</script>

<section>
	<h1 class="text-3xl font-semibold text-white">Matchs</h1>
	<div class="mt-2 bg-gray-800 rounded-lg sm:m-5">
		<Table {headers} {type} {actions} {parseItems} {addNew} {dbInfo} {filters} />
	</div>
</section>

<style></style>
