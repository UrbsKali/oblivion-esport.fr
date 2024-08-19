<script>
	// @ts-nocheck
	import { onMount } from 'svelte';

	let username = '';
	let tag = '';
	let tournamentCode = '';
	let MatchID = '';

	async function onSubmit() {
		/*
		url = f"https://europe.api.riotgames.com/riot/account/v1/accounts/by-riot-id/{username}/{tag}?api_key="
    response = requests.get(url)
    puuid = response.json().get("puuid")
    url = f"https://europe.api.riotgames.com/lol/match/v5/matches/by-puuid/{puuid}/ids?start=0&count=1&api_key=RGAPI-e0175c0f-e515-42f9-88a6-5af4b38b10c2"
    response = requests.get(url)
    match_id = response.json()[0]
    url = f"https://europe.api.riotgames.com/lol/match/v5/matches/{match_id}?api_key=RGAPI-e0175c0f-e515-42f9-88a6-5af4b38b10c2"
    response = requests.get(url)
    t_code = TournamentCode_entry.get()
		*/
		let resp = await fetch(
			`https://oblivion-esport.fr/riot.php?endpoint=/riot/account/v1/accounts/by-riot-id/${username}/${tag}&secret=oF8Hz9pNp9fDQoaYarAe`
		);
		let data = await resp.json();
		const puuid = data.puuid;
		resp = await fetch(
			`https://oblivion-esport.fr/riot.php?endpoint=/riot/match/v5/matches/by-puuid/${puuid}/ids&secret=oF8Hz9pNp9fDQoaYarAe`
		);
		data = await resp.json();
		console.log(data);
		const match_id = data[0];
		resp = await fetch(
			`https://oblivion-esport.fr/riot.php?endpoint=/riot/match/v5/matches/${match_id}&secret=oF8Hz9pNp9fDQoaYarAe`
		);
		data = await resp.json();
		if (data.metadata.tournamentCode === tournamentCode) {
			MatchID = match_id;
		} else {
			MatchID = 'No Match Found';
		}
	}
</script>

<section>
	<h1 class="text-3xl font-semibold text-gray-900 dark:text-white">MatchID Finder</h1>

	<div
		id="defaultModal"
		tabindex="-1"
		aria-hidden="true"
		class="overflow-y-auto overflow-x-hidden justify-center items-center w-full md:inset-0 h-modal md:h-full"
	>
		<div class="relative p-4 w-full max-w-2xl h-full md:h-auto">
			<!-- Modal content -->
			<div class="relative p-4 bg-white rounded-lg shadow dark:bg-gray-800 sm:p-5">
				<!-- Modal header -->
				<div
					class="flex justify-between items-center pb-4 mb-4 rounded-t border-b sm:mb-5 dark:border-gray-600"
				>
					<h3 class="text-lg font-semibold text-gray-900 dark:text-white">Find ID</h3>
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
								>Tournament Code</label
							>
							<input
								type="text"
								name="tournamentCode"
								id="tournamentCode"
								class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
								placeholder="EUW04df1-8b8f139f-9867-4808-b437-b752c2f93012"
								required=""
								bind:value={tournamentCode}
							/>
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
						<div>
							<label
								for="MatchID"
								class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Match ID</label
							>
							<input
								type="text"
								name="MatchID"
								id="MatchID"
								class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
								placeholder="No Match Found"
								disabled="true"
								bind:value={MatchID}
							/>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</section>

<style></style>
