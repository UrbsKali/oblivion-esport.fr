<script>
	export let name = [];
	export let logo = [];

	export let match = {};

	let score = [];
	if (match.winner) {
		score =
			match.winner.tag === match.team_one.tag
				? match.score.split('-')
				: match.score.split('-').reverse();
	}
</script>

<div
	class="flex items-center justify-between p-5 bg-gray-800 border border-gray-700 rounded-lg shadow-lg bg-opacity-40 backdrop-filter backdrop-blur-lg"
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
	<div class="flex h-full pl-3 ml-3 border-l-2 border-gray-700">
		<h1 class="self-center text-xl align-middle">
			{match.date ? new Date(match.date).toLocaleString().split(' ')[1].slice(0, -3) : ' '}
		</h1>
	</div>
</div>
