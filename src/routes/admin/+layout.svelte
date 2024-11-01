<script>
	// @ts-nocheck
	import { onMount } from 'svelte';
	import { supabase } from '$lib/supabaseClient';
	import { userdata } from '$lib/store';
	import { page } from '$app/stores';

	import UserBadge from '$lib/components/share/UserBadge.svelte';
	import SideBar from '$lib/components/admin/SideBar.svelte';

	let user;
	let mount = false;
	let sidebar = false;

	userdata.subscribe((value) => {
		if (value) {
			user = value;
			if (mount) checkPermission();
		}
	});

	page.subscribe((value) => {
		if (value) {
			if (mount) checkPermission();
		}
	});

	let menu = [
		{
			title: 'Accueil',
			uri: '/v2/admin',
			icon: 'pie-chart',
			permission: 'panel_admin'
		},
		{
			title: 'Gestion des données',
			icon: 'cube-outline',
			sub: [
				{
					title: 'Tournois',
					uri: '/v2/admin/tournaments',
					permission: 'edit_tournament'
				},
				{
					title: 'Teams',
					uri: '/v2/admin/teams',
					permission: 'edit_team'
				},
				{
					title: 'Matchs',
					uri: '/v2/admin/matches',
					permission: 'edit_match'
				}
			]
		},
		{
			title: 'Inscriptions',
			uri: '/v2/admin/register',
			icon: 'clipboard-outline',
			permission: 'register'
		},
		{
			title: 'Prédictions',
			uri: '/v2/admin/predictions',
			icon: 'analytics',
			permission: 'make_prediction'
		},
		{
			title: 'Utilisateurs',
			uri: '/v2/admin/users',
			icon: 'people-outline',
			permission: 'edit_user'
		},
		{
			title: 'MatchID Finder',
			uri: '/v2/admin/matchid',
			icon: 'search-outline',
			permission: 'matchid'
		},
		{
			title: 'Ressources & Liens',
			uri: '/v2/admin/ressources',
			icon: 'link',
			permission: 'panel_admin'
		},
		{
			title: 'Edition des tournois',
			uri: '/v2/admin/tournament-edition',
			icon: 'pencil-outline',
			permission: 'edit_tournament'
		},
		{
			title: 'Blog',
			uri: '/v2/admin/blog',
			icon: 'document-text',
			permission: 'edit_blog'
		}
	];

	let __menu = [];

	onMount(() => {
		mount = true;
		if (user) checkPermission();
	});

	function checkPermission() {
		// remove trailing slash if present
		let uri = window.location.pathname.endsWith('/')
			? window.location.pathname.slice(0, -1)
			: window.location.pathname;

		// check if user is allowed to acces admin pages
		if (!user.permissions) {
			// redirect to login page
			window.location.href = '/v2/login';
		}

		// check if user has permission to access the page
		let page = findURI(uri);

		if (!page) {
			// check page parent
			uri = uri.split('/').slice(0, -1).join('/');
			page = findURI(uri);

			if (!page) {
				window.location.href = '/v2/';
			}
		}
		console.log(page);

		if (!user.permissions.includes(page.permission)) {
			window.location.href = '/v2/';
		}
		__menu = menu.filter((item) => {
			if (item.sub) {
				item.sub = item.sub.filter((sub) => user.permissions.includes(sub.permission));
				return item.sub.length > 0;
			}
			return user.permissions.includes(item.permission);
		});
	}

	function findURI(uri) {
		// find the page in the menu, including sub uri
		let page = menu.find((item) => item.uri === uri);
		if (!page) {
			page = menu.find((item) => {
				if (item.sub) {
					return item.sub.find((sub) => sub.uri === uri);
				}
			});
		}
		// return the page : {uri, permission}
		try {
			const perms = page?.sub
				? page.sub.find((sub) => sub.uri === uri).permission
				: page.permission;
			return { uri: uri, permission: perms };
		} catch (e) {
			console.log(e);
			return null;
		}
	}
</script>

<div class="antialiased bg-gray-50 dark:bg-gray-900">
	<nav
		class="bg-white border-b border-gray-200 px-4 py-2.5 dark:bg-gray-800 dark:border-gray-700 fixed left-0 right-0 top-0 z-50"
	>
		<div class="flex flex-wrap items-center justify-between">
			<div class="flex items-center justify-start">
				<button
					data-drawer-target="drawer-navigation"
					data-drawer-toggle="drawer-navigation"
					aria-controls="drawer-navigation"
					on:click={() => (sidebar = !sidebar)}
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
				<a href="/v2/admin" class="flex items-center justify-between mr-4">
					<img src="/v2/oblivion.png" class="h-8 mr-3" alt="Oblivion Logo" />
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
	<SideBar menu={__menu} open={sidebar} />

	<main class="min-h-screen p-4 pt-20 md:ml-64">
		<slot />
	</main>
</div>
