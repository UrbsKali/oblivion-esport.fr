<script>
	// get url slug from router
	import { page } from '$app/stores';
	import { supabase } from '$lib/supabaseClient';

	import { userdata } from '$lib/store';

	import SvelteMarkdown from 'svelte-markdown';
	import { renderers } from '$lib/renderers';
	import RegisterButton from '$lib/components/others/RegisterButton.svelte';
	import Footer from '$lib/components/share/Footer.svelte';

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

			current_body = tournament?.slug?.body.Infos;
		}
	}
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
			<SvelteMarkdown source={current_body} {renderers} />
			<span>
				{#if current_button == 'Inscriptions'}
					{#if tournament?.can_register}
						<RegisterButton tournament_id={tournament?.id} />
					{:else}
						<button class="px-4 py-2 mt-5 text-white bg-gray-500 rounded-md"
							>Inscription fermée</button
						>
					{/if}
				{/if}
			</span>
		</div>
	</div>
</div>
<div class="mt-10">
	<Footer />
</div>
