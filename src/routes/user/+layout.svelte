<script>
	import { userdata } from '$lib/store';
	import { supabase } from '$lib/supabaseClient';
	import { page } from '$app/stores';

	let user;
	let path = '';

	page.subscribe((value) => {
		if (value) {
			path = value.url.pathname;
			console.log(path);
		}
	});

	userdata.subscribe((value) => {
		if (value) {
			user = value;
		}
	});

	async function handleImage(e) {
		const avatarFile = e.target.files[0];
		let extension = avatarFile.name.split('.').pop();
		const { data, error } = await supabase.storage
			.from('avatars')
			.upload(`${user.id}/avatar.${extension}`, avatarFile, {
				cacheControl: '3600',
				upsert: true
			});

		if (error) {
			console.error(error);
			if (error.message === 'Request failed with status code 413') {
				alert('Votre avatar est trop lourd (max 5 Mo)');
			} else {
				alert('Une erreur est survenue lors de la modification de votre avatar');
			}
		} else {
			const { data } = supabase.storage
				.from('avatars')
				.getPublicUrl(`${user.id}/avatar.${extension}`);
			user.avatar = data.publicUrl;
			userdata.set(user.publicUrl);

			const { data: data2, error: error2 } = await supabase
				.from('profiles')
				.update({ avatar_url: data.publicUrl })
				.eq('id', user.id);

			if (error2) {
				console.error(error2);
				alert('Une erreur est survenue lors de la modification de votre avatar');
			}

			alert(
				'Avatar modifié avec succès, le changement peut prendre quelques minutes pour être visible'
			);
		}
	}
</script>

<section>
	<div class="flex flex-col items-center justify-center pt-20">
		<div class="w-full overflow-hidden border-b border-gray-700 cross">
			<div class="flex flex-row items-center justify-center gap-5 mb-5 -mt-40">
				<div
					class="z-20 flex items-center justify-center bg-gray-800 rounded-full w-28 h-28"
					id="pp"
				>
					<label id="-label" class="z-10 rounded-full w-28" for="file">
						<span>Modifier</span>
					</label>
					<input
						type="file"
						name="file"
						id="file"
						accept="image/png, image/jpeg"
						on:change={handleImage}
					/>
					<img src={user?.avatar} alt="User avatar" class="rounded-full w-28 h-28" />
				</div>
				<div>
					<h1 class="mt-4 text-2xl font-semibold">{user?.name}</h1>
					<p class="text-sm text-gray-400">{user?.email}</p>
				</div>
			</div>
		</div>
		<!--{#if path.includes('notifications') || path.includes('settings') || path === '/user/'}-->
		<div class="flex flex-col items-center justify-center w-full px-5 my-5 sm:p-0">
			<div class="flex flex-row items-center justify-center w-full gap-5 sm:w-9/12 md:w-6/12">
				<a
					href="/user/"
					class="w-full p-2 text-sm text-center text-gray-400 bg-gray-900 bg-opacity-0 border border-gray-700 rounded-lg shadow hover:text-white sm:max-w-md backdrop-blur-sm"
					>Profil</a
				>

				<a
					href="/user/notifications"
					class="w-full p-2 text-sm text-center text-gray-400 bg-gray-900 bg-opacity-0 border border-gray-700 rounded-lg shadow hover:text-white sm:max-w-md backdrop-blur-sm"
					>Notifications</a
				>
				<a
					href="/user/settings"
					class="w-full p-2 text-sm text-center text-gray-400 bg-gray-900 bg-opacity-0 border border-gray-700 rounded-lg shadow hover:text-white sm:max-w-md backdrop-blur-sm"
				>
					Paramètres
				</a>
			</div>
		</div>
		<!--{:else}
		<div class="mb-5"></div>
		{/if}-->
	</div>
</section>
<slot />

<style>
	.cross::before {
		content: '';
		height: 240px;
		width: 100%;
		display: block;
		z-index: 0;
		position: relative;
		background-image: url('/plus-pattern-center.webp');
		background-size: 5%;
		background-repeat: repeat;
		opacity: 0.1;
		animation: move 20s linear infinite;
	}
	@keyframes move {
		0% {
			background-position: 0% 0%;
		}
		100% {
			background-position: 0% 230px;
		}
	}

	#-label {
		background-color: rgba(0, 0, 0, 0.5);
		aspect-ratio: 1;
		display: flex;
		opacity: 0;
		cursor: pointer;
		position: absolute;
		align-items: center;
		justify-content: center;
		transition: opacity 0.3s;
	}

	#pp:hover #-label {
		opacity: 1;
	}

	#-label span {
		font-size: 1.5rem;
	}

	#pp img {
		aspect-ratio: 1;
	}
	#pp input {
		display: none;
	}
</style>
