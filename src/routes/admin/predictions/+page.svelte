<script>
	// @ts-nocheck

	import Table from '$lib/components/Table.svelte';
	import { supabase } from '$lib/supabaseClient';
	import { onMount } from 'svelte';
	import { currentOrigin } from '$lib/config';
	import { userdata } from '$lib/store';
	import CrudForm from '$lib/components/CrudForm.svelte';
	import ReadModal from '$lib/components/ReadModal.svelte';

	const headers = ['Match', 'Tournoi', 'Prédiction', 'Score final', 'Actions'];
	let items = [];

	let m_id = 0;
	let selected_team = [];

	let user;

	const type = 'Prédiction';
	const type_accord = 'une';

	userdata.subscribe((value) => {
		if (value) {
			user = value;
		}
	});

	let handleSelectUpdate = async (e) => {
		// fetch teams options for the select field
		if (e.target.id == 'match') {
			let ids = e.target.selectedOptions[0].dataset.utils.split('#');
			const { data, error } = await supabase.from('Teams').select('name, id').in('id', ids);
			const teams_select = document.querySelector('#team');
			data?.forEach((el) => {
				fields[1].options = [...fields[1].options, { name: el.name, value: el.id }];
				const option = document.createElement('option');
				option.value = el.id;
				option.text = el.name;
				teams_select.add(option);
			});
		}
	};

	let fields = [
		{
			name: 'Match',
			id: 'match',
			type: 'select',
			required: true,
			options: [],
			wide: true,
			onChange: handleSelectUpdate
		},
		{
			name: 'Equipe Gagnante',
			id: 'team',
			type: 'select',
			required: true,
			options: []
		},
		{
			name: 'Score',
			id: 'score',
			type: 'text',
			required: true,
			placeholder: '0-0'
		}
	];

	async function addNew() {
		// fetch tournament options for the select field
		const { data, error } = await supabase
			.from('Matchs')
			.select(`id, team_one(name, id), team_two(name, id), tournament_id(title)`)
			.gte('date', new Date().toISOString());
		data?.forEach((element) => {
			let el = {
				text: `${element.team_one.name} vs ${element.team_two.name} - ${element.tournament_id.title}`,
				value: element.id,
				data: `${element.team_one.id}#${element.team_two.id}`
			};
			fields[0].options = [...fields[0].options, el];
		});

		new CrudForm({
			target: document.body,
			props: { fields, onSubmit, type: 'Prédiction', type_accord: 'une', open: true }
		});
	}

	async function onSubmit(e) {
		e.preventDefault();
		const btn = e.target;
		btn.disabled = true;
		btn.textContent = 'En cours...';
		btn.classList.add('cursor-not-allowed', 'opacity-50');
		const form = e.target.closest('form');
		const form_data = new FormData(form);
		const payload = {};
		for (const [key, value] of form_data.entries()) {
			payload[key] = value;
		}
		payload.made_by = (await supabase.auth.getUser())?.data?.user?.id;
		if (payload.score) {
			let score = payload.score.replaceAll(' ', '').split('-');
			score = score.map((el) => parseInt(el));
			score = score.sort((a, b) => b - a);
			payload.score = `${score[0]}-${score[1]}`;
		}
		const { data, error } = await supabase.from('Predictions').insert([payload]).select();
		if (error) {
			console.error(error);
			btn.disabled = false;
			btn.textContent = 'Erreur';
			btn.classList.toggle('cursor-not-allowed', 'opacity-50');
		}
		if (data) {
			window.location.reload();
		}
	}

	let handleDelete = async (e) => {
		e.preventDefault();
		const btn = e.target;
		btn.disabled = true;
		btn.textContent = 'En cours...';
		btn.classList.add('cursor-not-allowed', 'opacity-50');
		const id = e.target.closest('.popup').id.split('-')[2];
		const response = await supabase.from('Predictions').delete().eq('id', id);
		if (response.error) {
			console.error(response.error);
			btn.disabled = false;
			btn.textContent = 'Erreur';
			btn.classList.toggle('cursor-not-allowed', 'opacity-50');
		} else {
			window.location.reload();
		}
	};

	let filters = [
		{
			category: 'Tournoi',
			value: 'match.tournament_id.id',
			options: [
				{ name: 'TWC 5', value: 8 },
				{ name: 'EWC 1', value: 13 }
			]
		},
		{
			category: 'hidden',
			value: 'made_by',
			options: [{ name: 'current_user', value: user?.id, active: true }]
		}
	];
	$: {
		if (user?.role == 'admin' || user?.role == 'superadmin') filters[1].options = [];
		else if (filters.length > 1) filters[1].options[0].value = user?.id;
	}

	let actions = [
		{
			type: 'view',
			handler: async (e) => {
				e.preventDefault();
				const id = e.target.closest('tr').firstChild.dataset.utils;
				const { data, error } = await supabase
					.from('Predictions')
					.select(
						'id, match!inner(id, team_one(name, id), team_two(name, id), tournament_id!inner(title, id), winner(name, id), date, score), made_by(username), team(name, id), score'
					)
					.eq('id', id)
					.single();
				const values = {
					header: {
						title: `${data.match.team_one.name} vs ${data.match.team_two.name}`,
						date: data.match.date.split('T')[0]
					},
					body: [
						{
							label: 'Match',
							value: `${data.match.team_one.name} vs ${data.match.team_two.name}, ${data.match.tournament_id.title}`
						},
						{
							label: 'Prédictions',
							value: `Victoire de ${data.team.name}, ${data.score}` || 'Match non joué'
						},
						{
							label: 'Effectué par',
							value: data.made_by.username || ''
						}
					]
				};
				new ReadModal({
					target: document.body,
					props: {
						open: true,
						id: 'readModal-' + id,
						values,
						actions: [
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

	function parseItems(data) {
		let items = [];
		data?.forEach((element) => {
			let el = [
				{
					value: `${element.match.team_one.name} vs ${element.match.team_two.name}`,
					data: element.id
				},
				{ value: element.match.tournament_id.title, data: element.match.tournament_id.id },
				{
					value: `${element.team.name} - ${element.score}` || '-',
					data: element.team.id || ''
				},
				{ value: `${element.match.winner?.name || ''} - ${element.match.score}` || '-' }
			];
			items = [...items, el];
		});
		return items;
	}

	const dbInfo = {
		table: 'Predictions',
		key: 'id, match!inner(id, team_one(name, id), team_two(name, id), tournament_id!inner(title, id), winner(name, id), date, score), made_by, team(name, id), score'
	};
</script>

<section>
	<h1 class="text-3xl font-semibold text-gray-900 dark:text-white">Prédictions</h1>
	<Table {headers} {type} {type_accord} {actions} {dbInfo} {parseItems} {filters} {addNew} />
</section>

<style></style>
