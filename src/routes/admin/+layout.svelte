<script lang="ts">
	import { onMount } from 'svelte';
	import { supabase } from '$lib/supabaseClient';
	import UserBadge from '$lib/components/UserBadge.svelte';
	import SideBar from '$lib/components/SideBar.svelte';

	const menu = [
		{
			title: 'Accueil',
			uri: '/admin',
			icon: 'pie-chart'
		},
		{
			title: 'Inscriptions',
			icon: 'document-attach',
			sub: [
				{
					title: "Page d'inscription",
					uri: '/admin/wip'
				},
				{
					title: 'TWC 5',
					uri: '/admin/wip'
				}
			]
		},
		{
			title: 'Prédictions',
			icon: 'analytics',
			sub: [
				{
					title: 'TWC 5',
					uri: '/admin/wip'
				}
			]
		},
		{
			title: 'Utilisateurs',
			uri: '/admin/users',
			icon: 'people-circle'
		}
	];

	onMount(async () => {
		const {
			data: { user }
		} = await supabase.auth.getUser();
		if (!user) {
			window.location.pathname = '/login?redirect=' + window.location.pathname;
		}
	});
</script>

<div class="antialiased bg-gray-50 dark:bg-gray-900">
	<nav
		class="bg-white border-b border-gray-200 px-4 py-2.5 dark:bg-gray-800 dark:border-gray-700 fixed left-0 right-0 top-0 z-50"
	>
		<div class="flex flex-wrap justify-between items-center">
			<div class="flex justify-start items-center">
				<button
					data-drawer-target="drawer-navigation"
					data-drawer-toggle="drawer-navigation"
					aria-controls="drawer-navigation"
					class="p-2 mr-2 text-gray-600 rounded-lg cursor-pointer md:hidden hover:text-gray-900 hover:bg-gray-100 focus:bg-gray-100 dark:focus:bg-gray-700 focus:ring-2 focus:ring-gray-100 dark:focus:ring-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white"
				>
					<svg
						aria-hidden="true"
						class="w-6 h-6"
						fill="currentColor"
						viewBox="0 0 20 20"
						xmlns="http://www.w3.org/2000/svg"
					>
						<path
							fill-rule="evenodd"
							d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h6a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
							clip-rule="evenodd"
						></path>
					</svg>
					<svg
						aria-hidden="true"
						class="hidden w-6 h-6"
						fill="currentColor"
						viewBox="0 0 20 20"
						xmlns="http://www.w3.org/2000/svg"
					>
						<path
							fill-rule="evenodd"
							d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
							clip-rule="evenodd"
						></path>
					</svg>
					<span class="sr-only">Toggle sidebar</span>
				</button>
				<a href="/admin" class="flex items-center justify-between mr-4">
					<img src="/oblivion.png" class="mr-3 h-8" alt="Flowbite Logo" />
					<span class="self-center text-2xl font-semibold whitespace-nowrap dark:text-white"
						>Oblivion</span
					>
				</a>
			</div>
			<div class="flex items-center lg:order-2">
				<UserBadge />
			</div>
		</div>
	</nav>

	<!-- Sidebar -->
	<SideBar {menu} />

	<main class="p-4 md:ml-64 min-h-screen pt-20">
		<slot />
	</main>
</div>
