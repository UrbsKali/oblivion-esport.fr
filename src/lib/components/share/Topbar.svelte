<script>
	import { userdata } from '$lib/store';
	import { loadUserdata } from '$lib/utils';
	import { onMount } from 'svelte';
	import { page } from '$app/stores';
	import { hideOnClickOutside } from '$lib/utils';

	import UserBadge from './UserBadge.svelte';

	let user;
	let skip = false;
	let enable_cursor = true;
	let sidebar = false;

	userdata.subscribe((value) => {
		if (value) {
			user = value;
		}
	});

	page.subscribe((value) => {
		// only client side
		if (typeof window !== 'undefined') {
			enable_cursor = document.querySelector('.cursor') != null;
			if (!enable_cursor) {
				document.addEventListener('DOMContentLoaded', () => {
					enable_cursor = document.querySelector('.cursor') != null;
				});
			}
		}
	});

	onMount(async () => {
		if (!skip) await loadUserdata();
		enable_cursor = document.querySelector('.cursor') != null;
		// check after full page load
		if (!enable_cursor) {
			document.addEventListener('DOMContentLoaded', () => {
				enable_cursor = document.querySelector('.cursor') != null;
			});
		}

		hideOnClickOutside(document.querySelector('#sidebar'), () => {
			sidebar = false;
			console.log('hide');
		});
	});
</script>

<section class={enable_cursor ? 'enable-cursor' : ''}>
	<nav
		class="border-b px-4 py-2.5 border-gray-700 fixed left-0 right-0 top-0 z-50 backdrop-blur-lg"
	>
		<div class="flex flex-wrap items-center justify-between">
			<div class="flex items-center justify-start">
				<a href="/" class="flex items-center justify-between mr-4">
					<img src="/assets/oblivion.webp" class="h-12 mr-3" alt="Oblivion Logo" />
					<span class="self-center text-2xl font-semibold text-white whitespace-nowrap"></span>
				</a>
			</div>
			<div class="items-center hidden md:flex">
				<ul class="flex gap-10">
					<li>
						<a href="/" class="text-gray-400 hover:text-white">Actus</a>
					</li>
					<li>
						<a href="/tournaments" class="text-gray-400 hover:text-white">Tournois</a>
					</li>
					<li>
						<a href="/" class="text-gray-400 hover:text-white">Staff</a>
					</li>

					<li>
						<a href="/" class="text-gray-400 hover:text-white">Contact</a>
					</li>
				</ul>
			</div>
			<!--mobile sidebar-->
			<aside
				id="sidebar"
				class="fixed left-0 z-10 w-64 h-screen transition-transform {sidebar
					? 'translate-x-0'
					: '-translate-x-full'} md:hidden top-[68.8px] bg-gray-900 border-r border-gray-700 backdrop-blur-lg"
				aria-label="Sidebar"
			>
				<div class="h-full px-3 py-4 overflow-y-auto">
					<ul class="space-y-2 font-medium">
						<li>
							<a
								href="/"
								class="flex items-center p-2 border-gray-700 group focus:border-b hover:border-b"
							>
								<span class="ms-3">Blog</span>
							</a>
						</li>
						<li>
							<a
								href="/tournaments"
								class="flex items-center p-2 border-gray-700 group focus:border-b hover:border-b"
							>
								<span class="ms-3">Tournaments</span>
							</a>
						</li>
						<li>
							<a
								href="/"
								class="flex items-center p-2 border-gray-700 group focus:border-b hover:border-b"
							>
								<span class="ms-3">Staff</span>
							</a>
						</li>
						<li>
							<a
								href="/"
								class="flex items-center p-2 border-gray-700 group focus:border-b hover:border-b"
							>
								<span class="ms-3">Contact</span>
							</a>
						</li>
					</ul>
					<div class="flex items-center justify-between p-4 mt-2 border-t border-gray-700">
						{#if user}
							<UserBadge />
						{:else}
							<a
								class="inline-flex items-center px-3 py-2 m-auto text-sm font-medium text-center text-white border border-white rounded-lg focus:ring-4 focus:ring-white focus:outline-none bg-opacity-10 hover:bg-gray-900"
								href="/register"
							>
								Register
							</a>
							<a
								class="inline-flex items-center px-3 py-2 m-auto text-sm font-medium text-center text-white rounded-lg bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:outline-none focus:ring-primary-300 bg-primary-600 hover:bg-primary-700 focus:ring-primary-800"
								href="/login"
							>
								Login
							</a>
						{/if}
					</div>
				</div>
			</aside>
			<div class="gap-5">
				<div class="hidden md:block">
					{#if user}
						<UserBadge />
					{:else}
						<a
							class="inline-flex items-center px-3 py-2 m-auto text-sm font-medium text-center text-white border border-white rounded-lg focus:ring-4 focus:ring-white focus:outline-none bg-opacity-10 hover:bg-gray-900"
							href="/register"
						>
							Register
						</a>
						<a
							class="inline-flex items-center px-3 py-2 m-auto text-sm font-medium text-center text-white rounded-lg bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:outline-none focus:ring-primary-300 bg-primary-600 hover:bg-primary-700 focus:ring-primary-800"
							href="/login"
						>
							Login
						</a>
					{/if}
				</div>
				<button
					class="p-2 mr-2 text-gray-400 rounded-lg cursor-pointer md:hidden ring-1 ring-gray-700 hover:text-white"
					aria-label="Toggle sidebar"
					aria-expanded={sidebar}
					on:click={() => {
						sidebar = !sidebar;
					}}
				>
					<svg
						xmlns="http://www.w3.org/2000/svg"
						class="{sidebar ? 'hidden' : ''} w-6 h-6"
						fill="currentColor"
						stroke="currentColor"
						stroke-linecap="round"
						transform="scale(-1,1)"
						stroke-linejoin="round"
						stroke-width="2"
						viewBox="0 0 24 24"
					>
						<path d="M21 6H3M15 12H3M17 18H3" />
					</svg>
					<svg
						aria-hidden="true"
						class="{sidebar ? '' : 'hidden'} w-6 h-6"
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
			</div>
		</div>
	</nav>
</section>

<style>
</style>
