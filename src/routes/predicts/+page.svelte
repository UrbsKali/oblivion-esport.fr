<script>
	// @ts-nocheck
	import { onMount } from 'svelte';
	import { supabase } from '$lib/supabaseClient';
	import LinkButton from '$lib/components/LinkButton.svelte';
	import { pushState } from '$app/navigation';

	let predictions = [];
	let matchs = [];
	let date = new Date();
	let casteur = [];

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
				.select(`id, team_one(name), team_two(name), tournament_id(title), date`)
				.lte('date', `${dateStr}T23:59:59`)
				.gte('date', `${dateStr}T00:00:00`);
			matchs = data;
		}
		{
			const { data, error } = await supabase
				.from('Predictions')
				.select(`id, match, team(name), score, made_by(username)`)
				.in(
					'match',
					matchs.map((el) => el.id)
				);
			predictions = data;
		}
		// get each unique value of made_by
		casteur = [...new Set(predictions.map((el) => el.made_by.username))];
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
<table class=" border-spacing-5 border-separate">
	<thead>
		<tr>
			<th>Matchs</th>
			{#each casteur as cast}
				<th>{cast}</th>
			{/each}
		</tr>
	</thead>
	<tbody>
		{#each matchs as match}
			<tr>
				<td>
					<div
						class="bg-gray-700 text-gray-300 rounded-t-md flex flex-row items-center p-2 justify-center"
					>
						<div class="tournament p-1">{match.tournament_id.title}</div>
						-
						<div class="date p-1">{new Date(match.date).toLocaleTimeString()}</div>
					</div>
					<div class="bg-white dark:bg-gray-800 p-2 rounded-b-md flex flex-col">
						<div class="flex flex-row justify-center pt-2 pb-2">
							<div class="team p-1">{match.team_one.name}</div>
							<div class="vs p-1">vs</div>
							<div class="team p-1">{match.team_two.name}</div>
						</div>

						<LinkButton href="https://twitch.tv/kckitt_" text="Voir le match" />
					</div>
				</td>
				{#each casteur as cast}
					<td>
						{#if predictions.find((el) => el.made_by.username == cast && el.match == match.id)}
							{predictions.find((el) => el.made_by.username == cast && el.match == match.id).team
								.name} :
							{predictions.find((el) => el.made_by.username == cast && el.match == match.id).score}
						{:else}
							-
						{/if}
					</td>
				{/each}
			</tr>
		{/each}
	</tbody>
</table>
