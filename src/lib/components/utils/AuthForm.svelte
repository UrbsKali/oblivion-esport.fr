<script lang="ts">
	import { onMount } from 'svelte';
	import { supabase } from '$lib/supabaseClient';
	import SucessModal from '../modals/InfoModal.svelte';

	const AuthType = {
		login: 'Login',
		register: 'Inscription',
		reset: 'Mot de passe oublié'
	};

	export let redirect_uri = '/v2/';
	/**
	 * {'login' | 'register' | 'reset'}
	 */
	export let auth_type: string = AuthType.login;

	if (
		auth_type !== AuthType.login &&
		auth_type !== AuthType.register &&
		auth_type !== AuthType.reset
	) {
		// try to parse it
		auth_type = AuthType[auth_type.toLowerCase() as keyof typeof AuthType] || AuthType.login;
	}

	let loading = false;
	let email = '';
	let password = '';
	let accept_privacy = false;

	onMount(async () => {
		redirect_uri = parseRedirectURI(redirect_uri);
		const {
			data: { user },
			error
		} = await supabase.auth.getUser();
		if (user && auth_type === AuthType.login) {
			window.location.href = redirect_uri;
		}
		if (error && auth_type === AuthType.reset) {
			console.error(error);
			window.location.href = '/v2/login';
		}
		email = user?.email || '';
	});

	const handleLogin = async () => {
		try {
			loading = true;
			const { data, error } = await supabase.auth.signInWithPassword({
				email: email,
				password: password
			});
			if (error) throw error;
			if (data) {
				window.location.href = redirect_uri;
			}
		} catch (error) {
			if (error instanceof Error) {
				alert(error.message);
			}
		} finally {
			loading = false;
		}
	};

	const handleReset = async () => {
		try {
			loading = true;
			const { data, error } = await supabase.auth.updateUser({
				password: password
			});
			if (error) throw error;
			if (data) {
				window.location.href = redirect_uri;
			}
		} catch (error) {
			if (error instanceof Error) {
				alert(error.message);
			}
		} finally {
			loading = false;
		}
	};

	const handleAuth = async () => {
		if (auth_type === AuthType.login) {
			await handleLogin();
		} else if (auth_type === AuthType.reset) {
			await handleReset();
		} else {
			await handleRegister();
		}
	};

	async function handleRegister() {
		try {
			loading = true;
			if (!accept_privacy) {
				throw new Error(
					'Vous devez accepter la politique de confidentialité pour utiliser notre site'
				);
			}
			const { data, error } = await supabase.auth.signUp({
				email: email,
				password: password
			});
			if (error) throw error;
			if (data) {
				new SucessModal({
					target: document.body,
					props: {
						title: 'Inscription réussie',
						message: 'Un email de confirmation vous a été envoyé.',

						onClose: () => {
							window.location.href = '/v2/';
						}
					}
				});
			}
		} catch (error) {
			if (error instanceof Error) {
				alert(error.message);
			}
		} finally {
			loading = false;
		}
	}

	function parseRedirectURI(redirect_uri: string) {
		const urlParams = new URLSearchParams(window.location.search);
		const redirect = urlParams.get('redirect');
		if (redirect) {
			return redirect;
		} else if (redirect_uri == '/') {
			return window.location.origin;
		} else {
			return redirect_uri;
		}
	}
</script>

<section class="relative z-10">
	<div class="flex flex-col items-center justify-center px-6 py-8 mx-auto md:h-screen lg:py-0">
		<div
			class="w-full bg-gray-900 bg-opacity-0 border border-gray-700 rounded-lg shadow md:mt-0 sm:max-w-md xl:p-0 backdrop-blur-sm"
		>
			<div class="p-6 space-y-4 md:space-y-6 sm:p-8">
				<h1 class="text-xl font-bold leading-tight text-white tracking-tightmd:text-2xl">
					{auth_type}
				</h1>
				<form class="space-y-4 md:space-y-6" on:submit|preventDefault={handleAuth}>
					<div>
						<label for="email" class="block mb-2 text-sm font-medium text-white">Votre email</label>
						<input
							type="email"
							name="email"
							id="email"
							class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 bg-gray-700 border-gray-600 placeholder-gray-400 text-white focus:ring-blue-500 focus:border-blue-500 disabled:opacity-50"
							placeholder="kckitt@oblivion-esport.fr"
							disabled={auth_type === AuthType.reset}
							bind:value={email}
						/>
					</div>
					<div>
						<label for="password" class="block mb-2 text-sm font-medium text-white"
							>Votre mot de passe</label
						>
						<input
							type="password"
							name="password"
							id="password"
							placeholder="••••••••"
							class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 bg-gray-700 border-gray-600 placeholder-gray-400 text-white focus:ring-blue-500 focus:border-blue-500"
							bind:value={password}
						/>
					</div>
					{#if auth_type === AuthType.register}
						<div class="flex items-center justify-start">
							<input
								type="checkbox"
								id="privacy"
								bind:checked={accept_privacy}
								class="w-4 h-4 border border-gray-300 rounded text-primary-600 focus:ring-primary-600 focus:border-primary-600"
							/>
							<label for="privacy" class="ml-2 text-sm text-white"
								>J'accepte la <a
									href="/v2/policies/privacy"
									class="text-primary-100"
									target="_blank"
									>Politique de confidentialité
								</a></label
							>
						</div>
					{/if}

					<button
						type="submit"
						disabled={loading}
						class="w-full text-white bg-primary-300 hover:bg-primary-500 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center bg-primary-600 hover:bg-primary-700 focus:ring-primary-800"
						>{loading ? 'Chargement ...' : auth_type}</button
					>
				</form>
			</div>
		</div>
	</div>
</section>

<style lang="postcss">
</style>
