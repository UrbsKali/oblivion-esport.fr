<script>
	import { userdata } from '$lib/store';

	let user;

	userdata.subscribe((value) => {
		if (value) {
			user = value;
		}
	});
</script>

<section>
	<div class="flex flex-col items-center justify-center pt-20">
		<div class="w-full mb-10 overflow-hidden border-b border-gray-700 cross">
			<div class="flex flex-row items-center justify-center gap-5 mb-5 -mt-40">
				<div class="flex items-center justify-center bg-gray-800 rounded-full w-28 h-28">
					<img src={user?.avatar} alt="User avatar" class="rounded-full w-28 h-28" />
				</div>
				<div>
					<h1 class="mt-4 text-2xl font-semibold">{user?.name}</h1>
					<p class="text-sm text-gray-400">{user?.email}</p>
				</div>
			</div>
		</div>

		<div class="flex flex-col items-center justify-center">
			<div class="flex flex-row items-center justify-center gap-5">
				<a href="/v2/user/" class="text-sm text-gray-400 hover:text-white">Paramètres</a>
				<a href="/v2/user/teams" class="ml-4 text-sm text-gray-400 hover:text-white">Mes Équipes</a>
				<a href="/v2/user/tournaments" class="ml-4 text-sm text-gray-400 hover:text-white"
					>Mes Tournois</a
				>
			</div>
		</div>
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
		background-image: url('https://assets.codepen.io/1468070/plus-pattern-center.png');
		background-size: 5%;
		background-repeat: repeat;
		opacity: 0.1;
		animation: move 20s ease-in infinite;
	}
	@keyframes move {
		100% {
			background-position: 0% 100%;
		}
	}
</style>
