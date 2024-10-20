<script>
	import Table from '$lib/components/admin/Table.svelte';
	import CrudForm from '$lib/components/modals/CrudForm.svelte';
	import SucessModal from '$lib/components/modals/SucessModal.svelte';
	import { userdata } from '$lib/store';
	import { supabase } from '$lib/supabaseClient';

	let user;

	let filters = [];

	userdata.subscribe((value) => {
		if (value) {
			user = value;
			filters = [
				{
					category: 'hidden',
					value: 'member_of.uid.id',
					options: [{ value: user.id, active: true }]
				}
			];
		}
	});

	const dbInfo = {
		table: 'Teams',
		key: 'id, name, tag, member_of!inner(uid!inner(id, username, avatar_url)), logo_url'
	};

	const headers = ['Nom', 'Tag', 'Membres', 'Actions'];

	async function parseItems(data) {
		let items = [];
		for (let i = 0; i < data.length; i++) {
			let el = data[i];
			const { data: dat, error } = await supabase
				.from('member_of')
				.select('uid(username)')
				.eq('team_id', el.id);
			if (error) {
				console.error(error);
				return;
			}
			let el_ = [
				{ value: el.name, data: el.id, avatar: el.logo_url },
				{ value: el.tag },
				{ value: dat.map((el) => el.uid.username).join(', ') || '-' }
			];
			items.push(el_);
		}

		return items;
	}

	async function addNew() {
		new CrudForm({
			target: document.body,
			props: {
				fields: [
					{ name: 'Nom', type: 'text', id: 'name', required: true },
					{ name: 'TAG', type: 'text', required: true },
					{ name: 'Logo', type: 'img', required: true, wide: true },
					{
						name: 'Membre',
						id: 'member',
						type: 'text',
						value: user?.name,
						data: user?.id,
						required: true,
						readonly: true
					},
					{
						name: 'Rôle',
						type: 'select',
						id: 'role',
						required: true,
						options: [{ value: 'owner', text: 'Propriétaire', selected: true }],
						readonly: true
					},
					{
						name: 'Membre',
						id: 'member_1',
						type: 'autocomplete',
						value: '',
						data: '',
						required: true,
						onChange: async (e) => {
							// search through users
							const { data, error } = await supabase
								.from('profiles')
								.select('id, username')
								.ilike('username', `${e.target.value}*`)
								.range(0, 4);
							if (error) {
								console.error(error);
								return;
							}
							// create options
							let options = [];
							for (let i = 0; i < data.length; i++) {
								let el = data[i];
								options.push({ value: el.id, text: el.username });
							}
							return options;
						}
					},
					{
						name: 'Rôle',
						type: 'select',
						id: 'role_1',
						required: true,
						options: [
							{ value: 'player', text: 'Joueur' },
							{ value: 'substitute', text: 'Remplaçant' }
						]
					},
					{ type: 'duplicate', wide: true }
				],
				type_accord: 'une',
				type: 'Équipe',
				open: true,
				onSubmit: async (e) => {
					// get forms data
					e.preventDefault();
					const form_data = new FormData(e.target.closest('form'));
					let data = {};
					for (let [key, value] of form_data.entries()) {
						console.log(key, value);
						if (key.startsWith('member')) {
							const num = key.match(/\d+/g);
							if (!data.member) data.member = [];
							const uid = document.querySelector(`#${key}`).previousElementSibling.dataset.utils;
							let role_slug = 'role';
							if (num) role_slug += `_${num}`;
							data.member.push({ uid: uid, role: form_data.get(role_slug) });
						} else if (key.startsWith('role')) {
							continue;
						} else {
							data[key.toLowerCase()] = value;
						}
					}

					// create a hash for the team
					data.hash =
						Math.random().toString(36).substring(2, 15) +
						Math.random().toString(36).substring(2, 15);

					console.log(data);
					// upload logo
					const logoFile = form_data.get('logo');
					let extension = logoFile.name.split('.').pop();
					const { data: _, error: err } = await supabase.storage
						.from('avatars')
						.upload(`${user.id}/teams_${data.hash}.${extension}`, logoFile, {
							cacheControl: '3600',
							upsert: true
						});
					if (err) {
						console.error(err);
						alert("Une erreur est survenue lors de l'envoi du logo");
						return;
					}
					// get logo url
					const { data: data_ } = supabase.storage
						.from('avatars')
						.getPublicUrl(`${user.id}/teams_${data.hash}.${extension}`);
					data.logo_url = data_.publicUrl;

					// create team
					const team_data = { name: data.name, tag: data.tag, logo_url: data.logo_url };
					const { data: data__, error } = await supabase
						.from('Teams')
						.insert(team_data)
						.select('id')
						.single();
					if (error) {
						console.error(error);
						alert("Une erreur est survenue lors de la création de l'équipe");
						return;
					}
					console.log(data__);
					// add users to team
					for (const el in data.member) {
						console.log(el);
						const { data: data___, error: error__ } = await supabase
							.from('member_of')
							.insert({ team_id: data__.id, uid: el.uid, role: el.role });
						if (error__) {
							console.error(error__);
							alert("Une erreur est survenue lors de l'ajout d'un membre à l'équipe");
							return;
						}
					}
					new SucessModal({
						target: document.body,
						props: {
							message: "L'équipe a bien été créée",
							open: true,
							onClose: () => {
								window.location.reload();
							}
						}
					});
				}
			}
		});
	}
</script>

<div class="flex flex-col items-center justify-center px-5 py-0 mx-auto sm:p-0">
	<div
		class="w-full mt-0 bg-gray-900 bg-opacity-0 border border-gray-700 rounded-lg shadow sm:w-9/12 backdrop-blur-sm md:w-6/12"
	>
		<Table {dbInfo} {parseItems} {headers} {filters} {addNew} type="Équipe" type_accord="une" />
	</div>
</div>
