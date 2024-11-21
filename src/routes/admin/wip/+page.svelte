<script>
	import { supabase } from '$lib/supabaseClient';
	import { onMount } from 'svelte';

	onMount(async () => {
		const { data, error } = await supabase.from('stats_ewc').select('*');
		if (error) {
			console.error(error);
			return;
		}
		console.log(data);
	});
</script>

<h2 class="mb-4 text-4xl font-bold tracking-tight text-white">WIP</h2>
