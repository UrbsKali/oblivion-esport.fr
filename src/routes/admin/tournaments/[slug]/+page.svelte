<script>
	import Table from '$lib/components/admin/Table.svelte';
	import CrudForm from '$lib/components/modals/CrudForm.svelte';
	import SucessModal from '$lib/components/modals/InfoModal.svelte';
	import ReadModal from '$lib/components/modals/ReadModal.svelte';

	import { userdata } from '$lib/store';
	import { supabase } from '$lib/supabaseClient';
	import { page } from '$app/stores';
	import { onMount } from 'svelte';
	import { goto } from '$app/navigation';

	let slug;

	let tournament = {};

	page.subscribe(async (value) => {
		if (value) {
			slug = value.params.slug;
			await loadPage();
		}
	});

	async function loadPage() {
		const { data, error } = await supabase
			.from('Tournaments')
			.select('id, start, end, title, slug(*), can_register')
			.eq('slug', slug)
			.single();
		if (error) {
			console.error(error);
			return;
		}
		tournament = data;
	}
</script>

<div class="flex flex-col items-center justify-center px-5 py-0 mx-auto sm:p-0">
	<div class="flex items-center justify-start w-full mb-5">
		<button
			class="flex items-center justify-center w-10 h-10 text-gray-200 bg-gray-800 border border-gray-700 rounded-full shadow hover:bg-opacity-10"
			on:click={() => goto('/admin/tournaments', { replaceState: true })}
		>
			<svg
				class="w-5 h-5"
				aria-hidden="true"
				fill="currentColor"
				viewbox="0 0 20 20"
				xmlns="http://www.w3.org/2000/svg"
			>
				<path
					fill-rule="evenodd"
					d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
					clip-rule="evenodd"
				/>
			</svg>
		</button>
		<h2 class="text-2xl font-bold text-center text-gray-200" id="teamName">
			{tournament.title || slug}
		</h2>
		<button
			class="flex items-center justify-center w-10 h-10 text-gray-200 bg-gray-800 border border-gray-700 rounded-full shadow hover:bg-opacity-10"
			on:click={async (e) => {
				// delete team
				const confirmation = prompt('Voulez-vous vraiment supprimer cette équipe ? (oui/non)');
				if (confirmation !== 'oui') return;
				const { data, error } = await supabase.from('Tournaments').delete().eq('slug', slug);
				if (error) {
					console.error(error);
					alert("Une erreur est survenue lors de la suppression de l'équipe");
					return;
				}
				goto('/admin/tournaments', { replaceState: true });
			}}
		>
			<!--Trash icon-->
			<svg
				class="w-5 h-5 text-white hover:text-red-500"
				aria-hidden="true"
				xmlns="http://www.w3.org/2000/svg"
				width="24"
				height="24"
				fill="none"
				viewBox="0 0 24 24"
			>
				<path
					stroke="currentColor"
					stroke-linecap="round"
					stroke-linejoin="round"
					stroke-width="2"
					d="M5 7h14m-9 3v8m4-8v8M10 3h4a1 1 0 0 1 1 1v3H9V4a1 1 0 0 1 1-1ZM6 7h12v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7Z"
				/>
			</svg>
		</button>
	</div>
	<div
		class="w-full mt-0 bg-gray-800 border border-gray-700 rounded-lg shadow backdrop-blur-sm"
	></div>
</div>

<style>
	#teamName {
		width: calc(100% - 80px);
	}
</style>
