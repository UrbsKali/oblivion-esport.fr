<script>
	// get url slug from router
	import { page } from '$app/stores';
	import { supabase } from '$lib/supabaseClient';

	import { userdata } from '$lib/store';

	import SvelteMarkdown from 'svelte-markdown';
	import { renderers } from '$lib/renderers';
	import RegisterButton from '$lib/components/others/RegisterButton.svelte';
	import Footer from '$lib/components/share/Footer.svelte';
	import Bracket from '$lib/components/others/Bracket.svelte';
	import Pool from '$lib/components/others/Pool.svelte';

	let slug = '';
	let tournament = {};
	let user;

	let current_body = '';
	let buttons = ['Infos', 'Inscriptions', 'Règlement'];
	let current_button = 'Infos';

	userdata.subscribe((value) => {
		if (value) {
			user = value;
		}
	});

	page.subscribe(async (value) => {
		slug = value.params.slug;
		await loadPage();
	});

	async function loadPage() {
		const { data, error } = await supabase
			.from('Tournaments')
			.select('id, title, start, end, slug(body, image, last_update), can_register')
			.eq('slug', slug)
			.single();

		if (error) {
			console.error('error', error);
		} else {
			tournament = data;

			buttons = Object.keys(tournament?.slug?.body).map((key) => {
				return key;
			});
			// buttons = [...buttons, 'Play-offs', 'Matchs'];
			current_body = tournament?.slug?.body[buttons[0]] || '';
			current_button = buttons[0];
		}
	}

	let bracket = [
		{
			id: 1,
			name: 'KMF',
			logo: 'https://idlcqblimgotmibuednf.supabase.co/storage/v1/object/public/avatars/1846c911-521b-4338-85c5-835132e1765e/teams_r9dcj7llev9q1ynlttyxd.png'
		},
		{
			id: 2,
			name: 'NSE',
			logo: 'https://idlcqblimgotmibuednf.supabase.co/storage/v1/object/public/avatars/3799ee3f-255b-4aa8-8a86-cbc1bd66ee71/teams_1f6mfk5vkshix6qwqwgme.png'
		},
		{
			id: 3,
			name: 'FAS',
			logo: 'https://idlcqblimgotmibuednf.supabase.co/storage/v1/object/public/avatars/e7c2b0e7-f866-4e98-8c15-1e84918fe668/teams_mt27ui9z6hqs8sa1le7lf8.png'
		},
		{
			id: 4,
			name: 'GST',
			logo: 'https://idlcqblimgotmibuednf.supabase.co/storage/v1/object/public/avatars/5ba7937a-1670-49c1-ae18-7dc4c2f588de/teams_ys0vjs1mu8jjz87h9q38.png'
		},
		{
			id: 5,
			name: 'BZZ',
			logo: 'https://idlcqblimgotmibuednf.supabase.co/storage/v1/object/public/avatars/29342981-2bc0-4980-9b12-71fd5487c0b3/teams_jwmdgnihx6o1rchvqpzcv5.png'
		},
		{
			id: 6,
			name: 'WDE',
			logo: 'https://idlcqblimgotmibuednf.supabase.co/storage/v1/object/public/avatars/1850792e-7425-462d-9c14-0cc8e73cf73a/teams_w4irqztd5is726f7n6tun9.png'
		},
		{
			id: 7,
			name: 'NMS',
			logo: 'https://idlcqblimgotmibuednf.supabase.co/storage/v1/object/public/avatars/97e6631d-cef9-4614-9b3c-27f6674a59b5/teams_3bsy4jg8cn2wvvi1549gu.png'
		},
		{
			id: 8,
			name: 'ICA',
			logo: 'https://idlcqblimgotmibuednf.supabase.co/storage/v1/object/public/avatars/edff3703-0ac5-4af5-bfa4-216750cc2b2c/teams_671gl7tdzuhsoqytfztbp.jpg'
		}
	];
</script>

<div class="min-h-screen">
	<div
		id="hero"
		style="background-image: linear-gradient(0deg, rgba(14, 19, 31, 1) 0%, rgba(14, 19,31, 0.5) 100%), url({tournament
			?.slug?.image});"
		class="relative h-screen bg-center bg-no-repeat bg-cover"
	></div>

	<div
		class="container flex flex-col mt-[-60vh] items-center justify-center px-4 align-middle md:mx-auto relative w-full md:w-10/12"
	>
		<div class="w-full py-5">
			<h1 class="text-4xl font-bold">{tournament?.title || slug}</h1>
			<p class="text-lg text-gray-400">// {tournament?.start || ''} - {tournament?.end || ''}</p>
		</div>
		<div class="items-start w-full">
			<!-- tab system for the glassmorphism card below -->
			<div class="flex justify-start">
				{#each buttons as button, index}
					<button
						class="px-4 py-2 text-white transition-all border border-gray-700 {index == 0
							? 'rounded-ss-lg'
							: ''}
							{index == buttons.length - 1
							? 'rounded-se-lg'
							: ' '} bg-opacity-10 backdrop-blur-lg hover:bg-gray-700"
						on:click={() => {
							current_body = tournament?.slug?.body[button];
							current_button = button;
						}}
					>
						{button}
					</button>
				{/each}
			</div>
		</div>
		<div class="w-full p-5 border border-gray-700 rounded-b-lg rounded-e-lg backdrop-blur-lg">
			{#if current_button == 'Play-offs'}
				<div class="flex">
					<Bracket {bracket} />
				</div>
			{:else if current_button == 'Inscriptions'}
				<SvelteMarkdown source={current_body} {renderers} />

				{#if tournament?.can_register}
					<RegisterButton tournament_id={tournament?.id} />
				{:else}
					<button class="px-4 py-2 mt-5 text-white bg-gray-500 rounded-md"
						>Inscription fermée</button
					>
				{/if}
			{:else if current_button == 'Matchs'}
				<h1>WIP</h1>
			{:else if current_button == 'Phase de groupe'}
				<div class="flex flex-col w-full gap-5 justify-evenly md:flex-row">
					{#each current_body as pool}
						<Pool {pool} />
					{/each}
				</div>
			{:else}
				<SvelteMarkdown source={current_body} {renderers} />
			{/if}
		</div>
	</div>
</div>
<div class="mt-10">
	<Footer />
</div>
