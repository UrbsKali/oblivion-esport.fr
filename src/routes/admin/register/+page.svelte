<script>
	import { supabase } from '$lib/supabaseClient';

	import ReadModal from '$lib/components/modals/ReadModal.svelte';
	import Table from '$lib/components/admin/Table.svelte';

	const headers = ['Nom', 'Tournoi', 'Status', 'Actions'];
	const type = 'Teams';
	const type_accord = 'une';

	let actions = [
		{
			type: 'view',
			handler: async (e) => {
				e.preventDefault();
				e.stopPropagation();
				let tr = e.target.closest('tr');
				let name = tr.children[0].innerText;
				const id = tr.children[0].dataset.utils;
				const t_id = tr.children[1].dataset.utils;
				let tournament = tr.children[1].innerText;

				const { data, error } = await supabase
					.from('part_of')
					.select('status, why')
					.eq('team_id', id)
					.eq('tournament_id', t_id)
					.single();

				if (error) {
					console.error(error);
					return;
				}

				const status = data.status;
				const why = data.why;

				new ReadModal({
					target: document.body,
					props: {
						values: {
							header: {
								title: name,
								sub: tournament
							},
							body: [
								{
									label: 'Status',
									value: status
								},
								{
									label: 'Raison',
									value: why
								}
							]
						},
						actions: [
							{
								type: 'validate',
								title: 'Accepter',
								handler: async () => {
									const { error } = await supabase
										.from('part_of')
										.update({ status: 'accepted' })
										.eq('team_id', id)
										.eq('tournament_id', t_id);
									if (error) {
										console.error(error);
										alert('Une erreur est survenue');
									}

									window.location.reload();
								}
							},
							{
								type: 'delete',
								title: 'Refuser',
								handler: async () => {
									const { error } = await supabase
										.from('part_of')
										.update({ status: 'refused' })
										.eq('team_id', id)
										.eq('tournament_id', t_id);
									if (error) {
										console.error(error);
										alert('Une erreur est survenue');
									}

									window.location.reload();
								}
							}
						]
					}
				}).$on('close', () => {});

				e.stopPropagation();
			}
		}
	];

	let filters = [
		{
			category: 'Tournoi',
			value: 'part_of.tournament_id.id',
			options: [
				{ name: 'TWC 5', value: 8 },
				{ name: 'EWC 1', value: 13 }
			]
		},
		{
			category: 'Status',
			value: 'part_of.status',
			options: [
				{ name: 'En attente', value: 'pending', active: true },
				{ name: 'Accepté', value: 'accepted' },
				{ name: 'Refusé', value: 'refused' }
			]
		}
	];

	function parseItems(data) {
		let items = [];
		data?.forEach((element) => {
			for (let i = 0; i < element.part_of.length; i++) {
				const avatar = element.logo_url || '/assets/oblivion.webp';
				const el = [
					{ value: element.name, data: element.id, avatar: avatar },
					{
						value: element.part_of[i].tournament_id.title,
						data: element.part_of[i].tournament_id.id
					},
					{ value: element.part_of[i].status }
				];
				items = [...items, el];
			}
		});
		return items;
	}
	const dbInfo = {
		table: 'Teams',
		key: 'id, name, part_of!inner(team_id, tournament_id!inner(title, id), status), logo_url'
	};
</script>

<section>
	<h1 class="text-3xl font-semibold text-white sm:px-5">Inscriptions</h1>
	<div class="mt-2 bg-gray-800 rounded-lg sm:m-5">
		<Table {headers} {type} {type_accord} {parseItems} {dbInfo} {actions} {filters} />
	</div>
</section>

<style></style>
