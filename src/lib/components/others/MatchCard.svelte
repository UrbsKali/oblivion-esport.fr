<script>
	export let name = [];
	export let logo = [];

	export let match = {};
	export let showTime = true;

	let score = [];
	$: score = (() => {
		const s = (match?.score || '').toString();
		if (!s.includes('-')) return [' ', ' '];
		const parts = s.split('-').map((x) => x.trim());
		if (parts.length < 2) return [' ', ' '];
		// If DB provides winner and team tags, orient by winner
		if (match?.winner?.tag && match?.team_one?.tag && match?.team_two?.tag) {
			return match.winner.tag === match.team_one.tag ? parts : parts.slice().reverse();
		}
		// Otherwise assume provided order matches the team display order
		return parts;
	})();
</script>

<div
	class="flex items-center justify-between h-32 p-5 bg-gray-800 border border-gray-700 rounded-lg shadow-lg bg-opacity-40 backdrop-filter backdrop-blur-lg"
>
	<div class="flex justify-between w-full">
		<div class="flex flex-col gap-5">
			<div class="flex items-center gap-2 align-middle">
				<img src={logo[0]} alt={name[0]} class="w-8 h-8 rounded-full cover" />
				<p class="font-bold">{name[0]}</p>
			</div>
			<div class="flex items-center gap-2 align-middle">
				<img src={logo[1]} alt={name[1]} class="w-8 h-8 rounded-full cover" />
				<p class="font-bold">{name[1]}</p>
			</div>
		</div>
		<div class="flex flex-col items-center justify-center gap-5 text-xl font-bold">
			<p>{score[0] || ' '}</p>
			<p>{score[1] || ' '}</p>
		</div>
	</div>
	{#if showTime}
		{@const time = new Date(match.date)}
		<div class="flex h-full pl-3 ml-3 border-l-2 border-gray-700">
			<h1 class="self-center text-xl align-middle">
				{match.date ? time.toLocaleString().split(' ')[1].slice(0, -3) : ' '}
			</h1>
		</div>
	{/if}
</div>
