<script>
	// get url slug from router
	import { page } from '$app/stores';
	import { supabase } from '$lib/supabaseClient';

	import SvelteMarkdown from 'svelte-markdown';
	import { renderers } from '$lib/renderers';

	let slug = '';
	let tournament = {};

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

		console.log(data);
		if (error) {
			console.error('error', error);
		} else {
			tournament = data;
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
	<div class="container flex items-center justify-center px-4 align-middle sm:mx-auto">
		<div class="w-10/12 p-5 mt-[-25%] border border-gray-700 rounded-lg backdrop-blur-lg">
			<h1 class="text-4xl font-bold">{tournament?.title}</h1>
			<p class="text-lg text-gray-500">// {tournament?.start} - {tournament?.end}</p>
			<SvelteMarkdown source={tournament?.slug?.body} {renderers} />
			<span>
				{#if tournament?.can_register}
					<button class="px-4 py-2 mt-5 text-white rounded-md bg-primary-500">Inscription</button>
				{:else}
					<button class="px-4 py-2 mt-5 text-white bg-gray-500 rounded-md"
						>Inscription fermée</button
					>
				{/if}
			</span>
		</div>
	</div>
</div>
