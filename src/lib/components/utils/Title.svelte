<script>
	import { onMount } from 'svelte';

	export let size = 100;

	onMount(() => {
		const title = document.getElementById(`title-${size}`);

		// scale it, and add margin
		title.style.transform = `scale(${size / 100})`;
		title.style.margin = `${size / 4}px 0`;
	});
</script>

<div class="flex items-center transition-all align-center" id="title-{size}">
	<h1 class="text-6xl font-bold text-white">OBLIVI</h1>
	<div class="h-[60px] pt-[12px]">
		<img src="/assets/oblivion.png" alt="" class="h-[43px]" />
	</div>
	<h1 class="text-6xl font-bold text-white">N</h1>
</div>
