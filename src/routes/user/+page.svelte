<script>
	import { supabase } from '$lib/supabaseClient';
	import { userdata } from '$lib/store';

	let user;
	let loading = false;

	let new_username = '';
	let new_email = '';

	let discord;
	let rocket = '';
	let tracker = '';

	userdata.subscribe((value) => {
		if (value) {
			user = value;
			new_username = user.name;
			new_email = user.email;

			rocket = user.pseudo_rl;
			tracker = user.tracker;

			// check provider
			if (user.providers?.find((el) => el.provider === 'discord')) {
				const provider = user.providers.find((el) => el.provider === 'discord');
				discord = provider.identity_data.custom_claims.global_name;
			}
		}
	});

	async function handleSubmit() {
		loading = true;

		const { data, error } = await supabase
			.from('profiles')
			.update({ username: new_username })
			.eq('id', user.id);

		if (error) {
			console.error(error);
			alert('Une erreur est survenue lors de la modification de vos paramètres');
		} else {
			user.name = new_username;
			userdata.set(user);
		}

		loading = false;
	}

	async function handleInfo() {
		loading = true;

		// add @ on discord if not present
		if (!discord.startsWith('@')) {
			discord = '@' + discord;
		}

		const { data, error } = await supabase
			.from('profiles')
			.update({ discord, pseudo_rl: rocket, tracker })
			.eq('id', user.id);

		if (error) {
			console.error(error);
			alert('Une erreur est survenue lors de la modification de vos pseudos');
		}

		loading = false;
	}

	async function handleDiscord() {
		if (discord) {
			await unlinkDiscord();
		} else {
			await linkDiscord();
		}
	}

	async function linkDiscord() {
		const { data, error } = await supabase.auth.linkIdentity({
			provider: 'discord'
		});

		if (error) {
			console.error(error);
			alert('Une erreur est survenue lors de la liaison de votre compte Discord');
		}
	}

	async function unlinkDiscord() {
		// retrieve all identites linked to a user
		const identities = await supabase.auth.getUserIdentities();

		// find the discord identity
		const discordIdentity = identities.data.identities.find(
			(identity) => identity.provider === 'discord'
		);

		// unlink the discord identity
		const { error } = await supabase.auth.unlinkIdentity(discordIdentity);

		if (error) {
			console.error(error);
			alert('Une erreur est survenue lors de la suppression de la liaison de votre compte Discord');
		}

		discord = '';
		window.location.reload();
	}
</script>

<section class="relative z-10 pb-10">
	<div class="flex flex-col items-center justify-center px-5 py-0 mx-auto sm:p-0">
		<div
			class="w-full mt-0 bg-gray-900 bg-opacity-0 border border-gray-700 rounded-lg shadow sm:w-9/12 backdrop-blur-sm md:w-6/12"
		>
			<div class="flex flex-col w-full gap-5 p-6 space-y-4 md:space-y-6 sm:p-8">
				<form class="w-full space-y-4 md:space-y-6" on:submit|preventDefault={handleSubmit}>
					<div>
						<label for="username" class="block mb-2 text-sm font-medium text-white"
							>Votre username</label
						>
						<input
							type="text"
							name="username"
							id="username"
							class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 bg-gray-700 border-gray-600 placeholder-gray-400 text-white focus:ring-blue-500 focus:border-blue-500 disabled:opacity-50"
							placeholder="Mascode"
							bind:value={new_username}
						/>
					</div>
					<div>
						<label for="email" class="block mb-2 text-sm font-medium text-white">Votre email</label>
						<input
							type="email"
							name="email"
							id="email"
							class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 bg-gray-700 border-gray-600 placeholder-gray-400 text-white focus:ring-blue-500 focus:border-blue-500 disabled:opacity-50"
							placeholder="kckitt@oblivion-esport.fr"
							disabled
							bind:value={new_email}
						/>
					</div>

					<button
						type="submit"
						disabled={loading}
						class="w-full text-white bg-primary-300 hover:bg-primary-500 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center bg-primary-600 hover:bg-primary-700 focus:ring-primary-800"
						>{loading ? 'Chargement ...' : 'Modifer mes informations'}</button
					>
				</form>
				<form
					class="w-full pt-10 space-y-4 border-t border-gray-700 md:space-y-6"
					on:submit|preventDefault={handleInfo}
				>
					<!-- ask to link to discord with their logo on the btn -->
					<div>
						<label for="discord" class="block mb-2 text-sm font-medium text-white"
							>Lier vos comptes</label
						>
						<button
							class="min-w-28 text-white flex align-middle items-center justify-start bg-[#5865F2] focus:ring-4 focus:outline-nonefont-medium rounded-lg text-sm px-3 space-x-2 py-2.5 text-center"
							on:click={handleDiscord}
						>
							<svg
								width="32px"
								height="32px"
								viewBox="0 -28.5 256 256"
								version="1.1"
								xmlns="http://www.w3.org/2000/svg"
								xmlns:xlink="http://www.w3.org/1999/xlink"
								preserveAspectRatio="xMidYMid"
								fill="#000000"
								><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g
									id="SVGRepo_tracerCarrier"
									stroke-linecap="round"
									stroke-linejoin="round"
								></g><g id="SVGRepo_iconCarrier">
									<g>
										<path
											d="M216.856339,16.5966031 C200.285002,8.84328665 182.566144,3.2084988 164.041564,0 C161.766523,4.11318106 159.108624,9.64549908 157.276099,14.0464379 C137.583995,11.0849896 118.072967,11.0849896 98.7430163,14.0464379 C96.9108417,9.64549908 94.1925838,4.11318106 91.8971895,0 C73.3526068,3.2084988 55.6133949,8.86399117 39.0420583,16.6376612 C5.61752293,67.146514 -3.4433191,116.400813 1.08711069,164.955721 C23.2560196,181.510915 44.7403634,191.567697 65.8621325,198.148576 C71.0772151,190.971126 75.7283628,183.341335 79.7352139,175.300261 C72.104019,172.400575 64.7949724,168.822202 57.8887866,164.667963 C59.7209612,163.310589 61.5131304,161.891452 63.2445898,160.431257 C105.36741,180.133187 151.134928,180.133187 192.754523,160.431257 C194.506336,161.891452 196.298154,163.310589 198.110326,164.667963 C191.183787,168.842556 183.854737,172.420929 176.223542,175.320965 C180.230393,183.341335 184.861538,190.991831 190.096624,198.16893 C211.238746,191.588051 232.743023,181.531619 254.911949,164.955721 C260.227747,108.668201 245.831087,59.8662432 216.856339,16.5966031 Z M85.4738752,135.09489 C72.8290281,135.09489 62.4592217,123.290155 62.4592217,108.914901 C62.4592217,94.5396472 72.607595,82.7145587 85.4738752,82.7145587 C98.3405064,82.7145587 108.709962,94.5189427 108.488529,108.914901 C108.508531,123.290155 98.3405064,135.09489 85.4738752,135.09489 Z M170.525237,135.09489 C157.88039,135.09489 147.510584,123.290155 147.510584,108.914901 C147.510584,94.5396472 157.658606,82.7145587 170.525237,82.7145587 C183.391518,82.7145587 193.761324,94.5189427 193.539891,108.914901 C193.539891,123.290155 183.391518,135.09489 170.525237,135.09489 Z"
											fill="#fff"
											fill-rule="nonzero"
										>
										</path>
									</g>
								</g></svg
							>
							<p>{discord || 'Discord'}</p>
						</button>
					</div>
					<div>
						<label for="rocket" class="block mb-2 text-sm font-medium text-white"
							>Votre pseudo Rocket League</label
						>
						<input
							type="text"
							name="rocket"
							id="rocket"
							class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 bg-gray-700 border-gray-600 placeholder-gray-400 text-white focus:ring-blue-500 focus:border-blue-500 disabled:opacity-50"
							placeholder="Mascode"
							bind:value={rocket}
						/>
					</div>

					<div>
						<label for="tracker" class="block mb-2 text-sm font-medium text-white"
							>Votre lien de tracker</label
						>
						<input
							type="text"
							name="tracker"
							id="tracker"
							bind:value={tracker}
							class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 bg-gray-700 border-gray-600 placeholder-gray-400 text-white focus:ring-blue-500 focus:border-blue-500 disabled:opacity-50"
							placeholder="https://tracker.gg/rocket-league/profile/steam/76561198000000000"
						/>
					</div>

					<button
						type="submit"
						disabled={loading}
						class="w-full text-white bg-primary-300 hover:bg-primary-500 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center bg-primary-600 hover:bg-primary-700 focus:ring-primary-800"
						>{loading ? 'Chargement ...' : 'Enregistrer mes pseudos'}</button
					>
				</form>
			</div>
		</div>
	</div>
</section>
