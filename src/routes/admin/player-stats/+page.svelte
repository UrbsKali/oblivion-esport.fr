<script>
	// @ts-nocheck
	import { onMount } from 'svelte';
	import LoadingBar from '$lib/components/LoadingBar.svelte';

	let username = '';
	let tag = '';
	let count = '';

	let show_results = false;
	let loading = false;
	let loading_percentage = 0;
	let loading_message = '';
	let rawtournamentCodes = '';

	$: tournamentCodes = rawtournamentCodes
		.split('\n')
		.map((el) => el.trim())
		.filter((el) => el != '');

	let secret = 'oF8Hz9pNp9fDQoaYarAe';

	let stats = {
		kills: 0,
		deaths: 0,
		assists: 0,
		win: 0,
		lose: 0,
		total: 0,
		minions: 0,
		totalGold: 0,
		damageToChampions: 0,
		towerKill: 0
	};

	const maxPerSecond = 15;
	const maxPerTwoMinutes = 90;
	const maxTry = 3;
	let currentPerSecond = 0;
	let currentPerTwoMinutes = 0;
	setInterval(() => {
		currentPerSecond = 0;
	}, 1000);
	setInterval(() => {
		currentPerTwoMinutes = 0;
	}, 120000);

	async function getHandler(url, tryCount = 0) {
		while (currentPerSecond > maxPerSecond || currentPerTwoMinutes > maxPerTwoMinutes) {
			await new Promise((resolve) => setTimeout(resolve, 1000));
			loading_message = 'Rate limit exceeded, waiting ...';
		} // 20 requests per second
		currentPerSecond++;
		currentPerTwoMinutes++;
		const response = await fetch(url);
		let data;
		try {
			data = await response.json();
		} catch (e) {
			// recall the function if the response is not json
			if (tryCount >= maxTry) {
				throw new Error('Max try reached');
			}
			await new Promise((resolve) => setTimeout(resolve, 1000));
			data = await getHandler(url, tryCount + 1);
		}
		return data;
	}

	async function onSubmit(e) {
		e.preventDefault();
		if (username == '' || tag == '' || count == '' || rawtournamentCodes == '') {
			alert('Please fill all the fields');
			return;
		}
		loading = true;
		loading_percentage = 0;
		loading_message = 'Getting PUUID';
		loading_percentage = 1;
		let matchs = [];
		let data = await getHandler(
			`https://oblivion-esport.fr/api/riot.php?endpoint=/riot/account/v1/accounts/by-riot-id/${username}/${tag}&secret=${secret}`
		);
		const puuid = data.puuid;
		loading_message = 'Getting matchs';
		loading_percentage = 2;
		for (let i = 0; i < count / 100; i++) {
			data = await getHandler(
				`https://oblivion-esport.fr/api/riot.php?endpoint=/lol/match/v5/matches/by-puuid/${puuid}/ids?start=${i * 100}%26count%3D100&secret=${secret}`
			);
			console.log(data);
			if (data.length == 0) {
				break;
			}
			matchs = [...matchs, ...data];
		}
		loading_message = 'Getting stats from matchs (this may take a while)';
		loading_percentage = 3;

		let i = 0;
		let bar = new Promise((resolve) => {
			matchs.forEach(async (match) => {
				data = await getHandler(
					`https://oblivion-esport.fr/api/riot.php?endpoint=/lol/match/v5/matches/${match}&secret=${secret}`
				);
				loading_message = `Getting stats from matchs (this may take a while) ${i + 1}/${matchs.length}`;
				if (!tournamentCodes.includes(data.info.tournamentCode)) {
					i++;
					loading_percentage = (i / matchs.length) * 100;
					if (i == matchs.length - 1) {
						resolve();
					}
					return;
				}
				let participant = data.info.participants.find((el) => el.puuid == puuid);
				stats.kills += participant.kills;
				stats.deaths += participant.deaths;
				stats.assists += participant.assists;
				stats.totalGold += participant.goldEarned;
				stats.damageToChampions += participant.totalDamageDealtToChampions;
				stats.minions += participant.totalMinionsKilled;
				stats.towerKill += participant.turretKills;
				stats.total++;
				if (participant.win) {
					stats.win++;
				} else {
					stats.lose++;
				}
				i++;
				loading_percentage = (i / matchs.length) * 100;
				console.log(stats);
				if (i == matchs.length - 1) {
					resolve();
				}
			});
		});
		await bar;
		loading = false;
		show_results = true;
		console.log(stats);
	}
</script>

<section>
	<h1 class="text-3xl font-semibold text-gray-900 dark:text-white">Player Stats</h1>

	<div
		class="overflow-y-auto overflow-x-hidden justify-center items-center w-full md:inset-0 h-modal md:h-full"
	>
		<div class="relative p-4 w-full max-w-2xl h-full md:h-auto">
			<!-- Modal content -->
			<div class="relative p-4 bg-white rounded-lg shadow dark:bg-gray-800 sm:p-5">
				<!-- Modal header -->
				<div
					class="flex justify-between items-center pb-4 mb-4 rounded-t border-b sm:mb-5 dark:border-gray-600"
				>
					<h3 class="text-lg font-semibold text-gray-900 dark:text-white">Get Stats</h3>
				</div>
				<!-- Modal body -->
				<form action="#">
					<div class="grid gap-4 mb-4 sm:grid-cols-2">
						<div>
							<label
								for="Username"
								class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Username</label
							>
							<input
								type="text"
								name="Username"
								id="Username"
								class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
								placeholder="KcKitt_"
								required=""
								bind:value={username}
							/>
						</div>
						<div>
							<label for="tag" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
								>TAG</label
							>
							<input
								type="text"
								name="tag"
								id="tag"
								class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
								placeholder="EUW"
								required=""
								bind:value={tag}
							/>
						</div>
						<div class="col-span-2">
							<label
								for="tournamentCode"
								class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
								>Nombre de matchs à vérifier</label
							>
							<input
								type="number"
								name="tournamentCode"
								id="tournamentCode"
								class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
								placeholder="200"
								required="true"
								step="100"
								min="100"
								bind:value={count}
							/>
						</div>
						<div class="col-span-2">
							<label
								for="tournamentCode"
								class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
								>Code tournois de la Coupe, format : un code par ligne
							</label>
							<textarea
								name="tournamentCode"
								id="tournamentCode"
								rows="10"
								class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
								placeholder="EUW04dfb-4bf34c24-ecd1-42d6-931c-c36f3c88ffab
EUW04dfb-4bf34c24-ecd1-42d6-931c-c36f3c88ffab
EUW04dfb-4bf34c24-ecd1-42d6-931c-c36f3c88ffab
EUW04dfb-4bf34c24-ecd1-42d6-931c-c36f3c88ffab
EUW04dfb-4bf34c24-ecd1-42d6-931c-c36f3c88ffab
EUW04dfb-4bf34c24-ecd1-42d6-931c-c36f3c88ffab
EUW04dfb-4bf34c24-ecd1-42d6-931c-c36f3c88ffab
EUW04dfb-4bf34c24-ecd1-42d6-931c-c36f3c88ffab"
								bind:value={rawtournamentCodes}
							></textarea>
						</div>
						<div>
							<button
								type="submit"
								class="text-white inline-flex items-center bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800"
								on:click={onSubmit}
							>
								<svg
									xmlns="http://www.w3.org/2000/svg"
									x="0px"
									y="0px"
									width="100"
									height="100"
									viewBox="0 0 50 50"
									class="w-5 h-5 mr-2 -ml-2 fill-white"
								>
									<path
										d="M 21 3 C 11.601563 3 4 10.601563 4 20 C 4 29.398438 11.601563 37 21 37 C 24.355469 37 27.460938 36.015625 30.09375 34.34375 L 42.375 46.625 L 46.625 42.375 L 34.5 30.28125 C 36.679688 27.421875 38 23.878906 38 20 C 38 10.601563 30.398438 3 21 3 Z M 21 7 C 28.199219 7 34 12.800781 34 20 C 34 27.199219 28.199219 33 21 33 C 13.800781 33 8 27.199219 8 20 C 8 12.800781 13.800781 7 21 7 Z"
									></path>
								</svg>
								Rechercher
							</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
	{#if loading}
		<LoadingBar message={loading_message} percentage={loading_percentage} color="bg-primary-200" />
	{/if}

	{#if show_results}
		<div
			class="overflow-y-auto overflow-x-hidden justify-center items-center w-full md:inset-0 h-modal md:h-full"
		>
			<div class="relative p-4 w-full max-w-2xl h-full md:h-auto">
				<!-- Modal content -->
				<div class="relative p-4 bg-white rounded-lg shadow dark:bg-gray-800 sm:p-5">
					<!-- Modal header -->
					<div
						class="flex justify-between items-center pb-4 mb-4 rounded-t border-b sm:mb-5 dark:border-gray-600"
					>
						<h3 class="text-lg font-semibold text-gray-900 dark:text-white">Statistiques</h3>
					</div>
					<!-- Modal body -->
					<div>
						{#each Object.keys(stats) as k}
							<div class="flex justify-between items-center mb-4">
								<p class="text-sm font-medium text-gray-900 dark:text-white">{k}</p>
								<p class="text-sm font-medium text-gray-900 dark:text-white">{stats[k]}</p>
							</div>
						{/each}
					</div>
				</div>
			</div>
		</div>
	{/if}
</section>

<style></style>
