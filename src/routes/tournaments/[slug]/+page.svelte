<script>
	// get url slug from router
	import { page } from '$app/stores';
	import { onMount } from 'svelte';

	let slug = '';
	page.subscribe((value) => {
		slug = value.params.slug;
	});

	onMount(() => {
		console.log(slug);
	});
</script>

<div>
	<div class="border-b border-gray-700 landing">
		<div class="relative z-10 h-screen">
			<div class="flex items-center justify-center w-6/12 h-full">
				<div class="text-center">
					<h1 class="text-6xl font-bold text-gray-300">Tournament</h1>
					<p class="text-lg text-gray-500">// {slug}</p>
				</div>
			</div>
		</div>
	</div>
</div>
