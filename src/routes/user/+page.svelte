<script>
	import { supabase } from '$lib/supabaseClient';
	import { userdata } from '$lib/store';

	let user;
	let loading = false;

	let new_username = '';
	let new_email = '';
	let new_password = '';
	let new_password_confirmation = '';

	userdata.subscribe((value) => {
		if (value) {
			user = value;
			new_username = user.name;
			new_email = user.email;
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

	async function handlePassword() {
		loading = true;

		if (new_password !== new_password_confirmation) {
			alert('Les mots de passe ne correspondent pas');
			loading = false;
			return;
		}

		const { data, error } = await supabase.auth.updateUser({
			password: new_password
		});

		if (error) {
			console.error(error);
			alert('Une erreur est survenue lors de la modification de votre mot de passe');
		}

		loading = false;

		new_password = '';
		new_password_confirmation = '';
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
						>{loading ? 'Chargement ...' : 'Modifer mes paramètres'}</button
					>
				</form>
				<form
					class="w-full pt-10 space-y-4 border-t border-gray-700 md:space-y-6"
					on:submit={handlePassword}
				>
					<!--password reset, double field to check password-->
					<div>
						<label for="password" class="block mb-2 text-sm font-medium text-white"
							>Nouveau mot de passe</label
						>
						<input
							type="password"
							name="password"
							id="password"
							class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 bg-gray-700 border-gray-600 placeholder-gray-400 text-white focus:ring-blue-500 focus:border-blue-500 disabled:opacity-50"
							placeholder="********"
							bind:value={new_password}
						/>
					</div>

					<div>
						<label for="password" class="block mb-2 text-sm font-medium text-white"
							>Confirmer le mot de passe</label
						>
						<input
							type="password"
							name="password"
							id="password"
							class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 bg-gray-700 border-gray-600 placeholder-gray-400 text-white focus:ring-blue-500 focus:border-blue-500 disabled:opacity-50"
							placeholder="********"
							bind:value={new_password_confirmation}
						/>
					</div>
					<button
						type="submit"
						disabled={loading}
						class="w-full text-white bg-primary-300 hover:bg-primary-500 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center bg-primary-600 hover:bg-primary-700 focus:ring-primary-800"
						>{loading ? 'Chargement ...' : 'Changer mon mot de passe'}</button
					>
				</form>
			</div>
		</div>
	</div>
</section>
