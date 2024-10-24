<script>
	import Cursor from '$lib/components/share/Cursor.svelte';
	import Footer from '$lib/components/share/Footer.svelte';
	import { onMount } from 'svelte';

	let items = [
		{ title: 'ELEON WORLD CUP', content: 'Description for event 1' },
		{ title: 'Trankil World Cup 5', content: 'Description for event 2' },
		{ title: 'Trankil Invitational', content: 'Description for event 3' }
		// Add more items as needed
	];

	onMount(() => {
		// add the middle x and y coordinates for each .cup element
		const cups = document.querySelectorAll('.cup');
		cups.forEach((cup) => {
			const rect = cup.getBoundingClientRect();
			cup.dataset.middleX = rect.left + rect.width / 2;
			cup.dataset.middleY = rect.top + rect.height / 2;
		});
	});
</script>

<Cursor />
<div class="enable-cursor">
	<div class="container py-8 mx-auto">
		<div class="relative h-full p-10 overflow-hidden wrap">
			<div class="absolute h-full border-gray-700 sm:border border-2-2 left-1/2"></div>
			{#each items as item, index}
				<div class="flex flex-col items-center justify-center m-2 sm:flex-row">
					{#if index % 2 === 0}
						<div
							class="flex flex-col items-center justify-center p-5 border border-gray-700 rounded-lg cup"
						>
							<div class="flex flex-col items-center justify-center w-9/12 h-full">
								<div class="text-center">
									<h1 class="text-4xl font-bold text-gray-300 w-max">{item.title}</h1>
									<p class="text-lg text-gray-300">{item.content}</p>
									<div>
										<button class="px-4 py-2 mt-5 text-white border rounded-md border-primary-500"
											>En savoir +</button
										>
										<button class="px-4 py-2 mt-5 text-white rounded-md bg-primary-500"
											>Inscription</button
										>
									</div>
								</div>
							</div>
						</div>
						<div class="hidden w-6/12 sm:block aspect-square min-w-96" id="rl"></div>
					{:else}
						<div class="hidden w-6/12 sm:block aspect-square min-w-96" id="rl"></div>
						<div
							class="flex flex-col items-center justify-center p-5 border border-gray-700 rounded-lg cup"
						>
							<div class="flex flex-col items-center justify-center w-9/12 h-full">
								<div class="text-center">
									<h1 class="text-4xl font-bold text-gray-300 w-max">{item.title}</h1>
									<p class="text-lg text-gray-300">{item.content}</p>
									<div>
										<button class="px-4 py-2 mt-5 text-white border rounded-md border-primary-500"
											>En savoir +</button
										>
										<button class="px-4 py-2 mt-5 text-white rounded-md bg-primary-500"
											>Inscription</button
										>
									</div>
								</div>
							</div>
						</div>
					{/if}
				</div>
			{/each}
		</div>
	</div>
</div>
<Footer />

<style>
	.cup::after {
		content: '';
		position: absolute;
		width: 42px;
		aspect-ratio: 1;
		border-radius: 50%;
		border-color: #374151;
		backdrop-filter: blur(12px);
		border-width: 2px;
		left: calc(50% - 20px);
		top: data(middleY);
	}
	@media (max-width: 640px) {
		.cup::after {
			content: none !important;
		}
	}
</style>
