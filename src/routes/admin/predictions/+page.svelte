<script>
	// @ts-nocheck

	import Table from '$lib/components/Table.svelte';
	import { supabase } from '$lib/supabaseClient';
	import { onMount } from 'svelte';
	import { currentOrigin } from '$lib/config';

	const headers = ['Match', 'Tournoi', 'Prédiction', 'Score final', 'Actions'];
	let items = [];

	let teams_options = [];
	let m_id = 0;
	let selected_team = [];

	let current_user = {};
	let current_role = '';

	const type = 'Prédiction';
	const type_accord = 'une';

	let fields = [
		{
			name: 'Match',
			id: 'match',
			type: 'select',
			required: true,
			options: [],
			wide: true
		},
		{
			name: 'Equipe Gagnante',
			id: 'team',
			type: 'select',
			required: true,
			options: [...teams_options]
		},
		{
			name: 'Score',
			id: 'score',
			type: 'text',
			required: true,
			placeholder: '0-0'
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
		payload.made_by = (await supabase.auth.getUser())?.data?.user?.id;
		const { ret, error } = await supabase.from('Predictions').insert([payload]);
		if (error) {
			console.error(error);
		} else {
			let data, error;
			if (['superadmin', 'admin'].includes(current_role)) {
				({ data, error } = await supabase
					.from('Predictions')
					.select(
						`id, match(id, team_one(name, id), team_two(name, id), tournament_id(title, id), winner(name, id), date, score), made_by, team(name, id), score`
					));
			} else {
				({ data, error } = await supabase
					.from('Predictions')
					.select(
						`id, match(id, team_one(name, id), team_two(name, id), tournament_id(title, id), winner(name, id), date, score), made_by, team(name, id), score`
					)
					.eq('made_by', current_user.id));
			}
			items = [];
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
			const modal = FlowbiteInstances.getInstance('Modal', 'CrudModal');
			modal.hide();
		}
	};

	let handleDelete = async (e) => {
		e.preventDefault();
		let tr = e.target.closest('tr');
		let id = tr.children[0].dataset.utils;
		const response = await supabase.from('Predictions').delete().eq('id', id);
		if (response.error) {
			console.error(response.error);
		} else {
			items = items.filter((el) => el[0].data != id);
		}
	};

	let handleSelectUpdate = async (e) => {
		// fetch teams options for the select field
		if (e.target.id == 'match') {
			let ids = e.target.selectedOptions[0].dataset.utils.split('#');
			const { data, error } = await supabase.from('Teams').select('name, id').in('id', ids);
			data?.forEach((el) => {
				fields[1].options = [...fields[1].options, { name: el.name, value: el.id }];
			});
		}
	};

	onMount(async () => {
		{
			const { data, error } = await supabase.auth.getSession();
			if (error) {
				console.error(error);
				window.location.href = `${currentOrigin()}/login?redirect=${window.location.pathname}`;
			}
			current_user = data.session.user;
		}
		{
			const { data, error } = await supabase
				.from('profiles')
				.select('role')
				.eq('id', current_user.id);
			if (error) {
				console.error(error);
				window.location.href = `${window.location.origin}/`;
			}
			current_role = data[0].role;
		}
		{
			// fetch tournament options for the select field
			const { data, error } = await supabase
				.from('Matchs')
				.select(`id, team_one(name, id), team_two(name, id), tournament_id(title)`)
				.gte('date', new Date().toISOString());
			data?.forEach((element) => {
				let el = {
					name: `${element.team_one.name} vs ${element.team_two.name} - ${element.tournament_id.title}`,
					value: element.id,
					data: `${element.team_one.id}#${element.team_two.id}`
				};
				fields[0].options = [...fields[0].options, el];
			});
		}
		{
			let data, error;
			if (['superadmin', 'admin'].includes(current_role)) {
				({ data, error } = await supabase
					.from('Predictions')
					.select(
						`id, match(id, team_one(name, id), team_two(name, id), tournament_id(title, id), winner(name, id), date, score), made_by, team(name, id), score`
					));
			} else {
				({ data, error } = await supabase
					.from('Predictions')
					.select(
						`id, match(id, team_one(name, id), team_two(name, id), tournament_id(title, id), winner(name, id), date, score), made_by, team(name, id), score`
					)
					.eq('made_by', current_user.id));
			}
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
		}
	});
</script>

<section>
	<h1 class="text-3xl font-semibold text-gray-900 dark:text-white">Prédictions</h1>
	<Table
		{headers}
		{items}
		{type}
		{type_accord}
		{fields}
		{handleSubmit}
		{handleDelete}
		{handleSelectUpdate}
	/>
</section>

<style></style>
