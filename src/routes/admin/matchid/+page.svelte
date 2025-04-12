<script>
	// @ts-nocheck
	import { onMount } from 'svelte';
	import { supabase } from '$lib/supabaseClient';

	let username = '';
	let tag = '';
	let tournamentCode = '';
	let MatchID = '';

	async function onSubmit() {
		// use function to get the match id from riot api

		if (username == '' || tag == '' || tournamentCode == '') {
			alert('Please fill all fields');
			return;
		}

		const { data, error } = await supabase.functions.invoke('fetch-match-id', {
			body: { user: { name: username, tag: tag }, tournament_code: tournamentCode }
		});

		if (data.info.tournamentCode == tournamentCode) {
			MatchID = match_id;
			console.log(MatchID);
		} else {
			MatchID = 'No Match Found';
		}
	}
</script>

<section>
	<h1 class="text-3xl font-semibold text-white">MatchID Finder</h1>

	<div
		id="defaultModal"
		tabindex="-1"
		aria-hidden="true"
		class="items-center justify-center w-full overflow-x-hidden overflow-y-auto md:inset-0 h-modal md:h-full"
	>
		<div class="relative w-full h-full max-w-2xl p-4 md:h-auto">
			<!-- Modal content -->
			<div class="relative p-4 bg-gray-800 rounded-lg shadow sm:p-5">
				<!-- Modal header -->
				<div
					class="flex items-center justify-between pb-4 mb-4 border-b border-gray-600 rounded-t sm:mb-5"
				>
					<h3 class="text-lg font-semibold text-white">Find ID</h3>
				</div>
				<!-- Modal body -->
				<form action="#">
					<div class="grid gap-4 mb-4 sm:grid-cols-2">
						<div>
							<label for="Username" class="block mb-2 text-sm font-medium text-white"
								>Username</label
							>
							<input
								type="text"
								name="Username"
								id="Username"
								class=" border text-sm rounded-lg block w-full p-2.5 bg-gray-700 border-gray-600 placeholder-gray-400 text-white focus:ring-primary-500 focus:border-primary-500"
								placeholder="KcKitt_"
								required=""
								bind:value={username}
							/>
						</div>
						<div>
							<label for="tag" class="block mb-2 text-sm font-medium text-white">TAG</label>
							<input
								type="text"
								name="tag"
								id="tag"
								class=" border text-sm rounded-lg block w-full p-2.5 bg-gray-700 border-gray-600 placeholder-gray-400 text-white focus:ring-primary-500 focus:border-primary-500"
								placeholder="EUW"
								required=""
								bind:value={tag}
							/>
						</div>
						<div class="col-span-2">
							<label for="tournamentCode" class="block mb-2 text-sm font-medium text-white"
								>Tournament Code</label
							>
							<input
								type="text"
								name="tournamentCode"
								id="tournamentCode"
								class=" border text-sm rounded-lg block w-full p-2.5 bg-gray-700 border-gray-600 placeholder-gray-400 text-white focus:ring-primary-500 focus:border-primary-500"
								placeholder="EUW04df1-8b8f139f-9867-4808-b437-b752c2f93012"
								required=""
								bind:value={tournamentCode}
							/>
						</div>
						<div>
							<button
								type="submit"
								class="text-white inline-flex items-center bg-primary-600 hover:bg-primary-700 focus:ring-4 focus:outline-none focus:ring-primary-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center"
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
							<label for="MatchID" class="block mb-2 text-sm font-medium text-white">Match ID</label
							>
							<input
								type="text"
								name="MatchID"
								id="MatchID"
								class="bg-gray-700 border border-gray-600 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 placeholder-gray-400 text-white"
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
