<script>
	// @ts-nocheck
	import { onMount } from 'svelte';
	import { supabase } from '$lib/supabaseClient';
	import LinkButton from '$lib/components/utils/LinkButton.svelte';
	import { pushState } from '$app/navigation';

	let predictions = [];
	let matchs = [];
	let date = new Date();
	let casteur = [];
	let casteur_leaderboard = [];

	let tournament = '';
	let use_score = true;

	onMount(async () => {
		// check url date parameter
		const urlParams = new URLSearchParams(window.location.search);
		const dateParam = urlParams.get('date');
		if (dateParam) {
			date = new Date(dateParam);
		}
		fetchinfo(date);
	});

	async function fetchinfo(date) {
		let dateStr = date.toISOString().split('T')[0];
		{
			// get matchs
			const { data, error } = await supabase
				.from('Matchs')
				.select(
					`id, team_one(name, logo_url), team_two(name, logo_url), tournament_id(title), date`
				)
				.lte('date', `${dateStr}T23:59:59`)
				.gte('date', `${dateStr}T00:00:00`)
				.order('date');
			matchs = data;
		}
		{
			const { data, error } = await supabase
				.from('Predictions')
				.select(
					`id, match, team(name, logo_url), score, made_by(username, predict_accuracy), result`
				)
				.in(
					'match',
					matchs.map((el) => el.id)
				);
			predictions = data;
			console.log(predictions);
		}
		// get each unique value of made_by
		predictions?.forEach((element) => {
			if (!element.score) use_score = false;
			if (casteur.map((el) => el.username).includes(element.made_by.username)) return;
			casteur = [
				...casteur,
				{
					username: element.made_by.username,
					predict_accuracy: element.made_by.predict_accuracy || 0
				}
			];
		});

		casteur_leaderboard = casteur
			.sort((a, b) => {
				if (a.predict_accuracy == b.predict_accuracy) {
					if (a.username == 'KcKitt') return -1;
					if (b.username == 'KcKitt') return 1;
					return 0;
				}
				return -a.predict_accuracy + b.predict_accuracy;
			})
			.slice(0, 3);
		if (casteur_leaderboard.length < 3) {
			for (let i = casteur_leaderboard.length; i < 3; i++) {
				casteur_leaderboard.push({ username: 'N/A', predict_accuracy: 0 });
			}
		}

		// update url to current date, without reloading the page
		pushState(`${window.location.origin}${window.location.pathname}?date=${dateStr}`);
	}

	const dateOfXDay = (xDay = 1) => {
		let today = new Date(date);
		today.setDate(today.getDate() + xDay);
		date = today;
		return today;
	};
</script>

<LinkButton
	href=""
	text=""
	revert="true"
	target=""
	onClick={async (e) => await fetchinfo(dateOfXDay(-1))}
/>
<LinkButton href="" text="" target="" onClick={async (e) => await fetchinfo(dateOfXDay(1))} />
<h2>{date.toLocaleDateString()}</h2>
<div class="flex flex-row justify-between w-full min-h-screen">
	<div class="flex flex-row w-full horizontal-scroll-except-first-column">
		<div class="hideout"></div>

		<table class="-ml-10 border-separate table-fixed border-spacing-x-10 border-spacing-y-2">
			<thead>
				<tr>
					<th class="w-96">Matchs</th>
					{#each casteur as cast}
						<th
							><div class=" w-36">
								<div class="text-2xl font-extrabold">
									{Math.round(cast.predict_accuracy * 100)}%
								</div>
								<div class="uppercase">{cast.username}</div>
							</div>
						</th>
					{/each}
				</tr>
			</thead>
			<tbody>
				{#each matchs as match, i}
					<tr>
						<td class="w-96">
							<div
								class="flex flex-row items-center justify-center p-2 text-gray-300 bg-gray-700 rounded-t-md"
							>
								<div class="p-1 tournament">{match.tournament_id.title}</div>
								-
								<div class="p-1 date">{new Date(match.date).toLocaleTimeString()}</div>
							</div>
							<div class="flex flex-col p-2 bg-white dark:bg-gray-800 rounded-b-md">
								<div class="grid justify-center pt-2 pb-2 custom-grid">
									<div class="flex flex-col items-center p-1 team">
										<img
											src={match.team_one.logo_url}
											alt=""
											srcset=""
											class=" w-auto h-20 aspect-auto}"
										/>
										{match.team_one.name}
									</div>
									<div class="flex items-center p-1 vs"><img src="/assets/epee.png" alt="" /></div>
									<div class="flex flex-col items-center p-1 team">
										<img src={match.team_two.logo_url} alt="" srcset="" class="w-auto h-20" />
										{match.team_two.name}
									</div>
								</div>
							</div>
						</td>
						{#each casteur as cast, index}
							<td class="h-auto">
								{#if predictions.find((el) => el.made_by.username == cast.username && el.match == match.id)}
									<div class="flex flex-col items-center align-middle h-36">
										<img
											src={predictions.find(
												(el) => el.made_by.username == cast.username && el.match == match.id
											).team.logo_url}
											alt=""
											class=" w-auto h-20 m-auto {predictions.find(
												(el) => el.made_by.username == cast.username && el.match == match.id
											).result == 'ZERO'
												? 'bnw'
												: ''}"
										/>
									</div>
									{#if use_score}
										<div
											class="flex flex-col items-center align-middle h-12 {predictions.find(
												(el) => el.made_by.username == cast.username && el.match == match.id
											).result == 'FULL'
												? 'text-green-500'
												: predictions.find(
															(el) => el.made_by.username == cast.username && el.match == match.id
													  ).result == 'ZERO'
													? 'text-gray-600 italic'
													: ''}"
										>
											{predictions.find(
												(el) => el.made_by.username == cast.username && el.match == match.id
											).score}
										</div>
									{:else}
										<div class="h-12"></div>
									{/if}
								{:else}
									-
								{/if}
							</td>
							{#if index == casteur.length - 1 && i == 1}
								<td class="overflow-hidden"> </td>
							{/if}
						{/each}
					</tr>
				{/each}
			</tbody>
		</table>
		<div>
			<div class="max-w-sm mx-auto mt-16 bg-gray-800 rounded-md shadow-md min-w-60">
				<div class="px-4 py-2 bg-gray-700 rounded-t-md">
					<h2 class="text-xl font-semibold text-gray-300">Leaderboard</h2>
				</div>
				<ul class="divide-y divide-gray-200">
					{#each casteur_leaderboard as cast, index}
						<li class="flex items-center px-6 py-4">
							<span class="mr-4 text-lg font-medium text-gray-300">{index + 1}.</span>
							<div class="flex-1">
								<h3 class="text-lg font-medium text-gray-300">{cast.username}</h3>
								<p class="text-base text-gray-300">{Math.round(cast.predict_accuracy * 100)} %</p>
							</div>
							<img src="/assets/{index + 1}.png" alt="" class="ml-5 size-10" />
						</li>
					{/each}
				</ul>
			</div>
		</div>
	</div>
</div>

<style>
	.bnw {
		filter: grayscale(1);
		z-index: 0;
	}
	.custom-grid {
		grid-template-columns: 1fr 50px 1fr;
	}
	.horizontal-scroll-except-first-column {
		width: 100%;
		overflow: auto;
	}

	.horizontal-scroll-except-first-column > table {
		margin-left: 20em;
	}

	.horizontal-scroll-except-first-column > table > * > tr > th:first-child,
	.horizontal-scroll-except-first-column > table > * > tr > td:first-child {
		position: absolute;
		margin-left: -23em;
		background-color: #111827;
		z-index: 2;
	}

	.horizontal-scroll-except-first-column > table > * > tr > th,
	.horizontal-scroll-except-first-column > table > * > tr > td {
		/* Without this, if a cell wraps onto two lines, the first column
   * will look bad, and may need padding. */
		white-space: nowrap;
	}
	.hideout {
		position: absolute;
		width: 23em;
		background-color: #111827;
		height: 100%;
		z-index: 0;
	}

	.w-custom {
		width: calc(100vw + 20px);
	}

	/*Mobile, let the whole table scroll*/
	@media screen and (max-width: 640px) {
		.horizontal-scroll-except-first-column {
			overflow: auto;
			margin-left: -3em;
		}
		.horizontal-scroll-except-first-column > table {
			margin-left: 0;
			padding-left: 0;
		}
		.horizontal-scroll-except-first-column > table > * > tr > th:first-child,
		.horizontal-scroll-except-first-column > table > * > tr > td:first-child {
			position: relative;
		}
		.hideout {
			display: none;
		}
	}

	/*Scrollbar styling*/

	.horizontal-scroll-except-first-column::-webkit-scrollbar {
		height: 12px;
		margin-left: 25em;
		width: 500px;
		background-color: #111827;
	}
	.horizontal-scroll-except-first-column::-webkit-scrollbar-thumb {
		background-color: #4b5563;
		border-radius: 10px;
	}
</style>
