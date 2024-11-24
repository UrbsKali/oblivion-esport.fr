<script>
	// the idea is to recursively call the bracket component with a smaller and smaller array (cut down in half each time)
	// as the array reaches 1 item, the idea is to then include such item in a heading
	// selectWinners allows to halve the array by selecting one item from each couplet
	function selectWinners(bracket) {
		return bracket.reduce((acc, team, i) => {
			if (i % 2 === 0) acc.push(team);
			return acc;
		}, []);
	}
	export let bracket;
	// describe the winner as the only item of the input array, if existing
	$: winner = bracket.length === 1 && bracket[0];
</script>

<!-- display the winner in a heading or an unordered list with the teams if no winner is identified -->
{#if winner}
	<div class="flex items-center justify-center gap-5 mx-8 align-middle">
		<span><img src={winner.logo} alt={winner.name} class="w-8 h-8 rounded-full" /></span>
		<span class="text-3xl font-bold">{winner.name}</span>
	</div>
{:else}
	<ul>
		{#each bracket as team (team.id)}
			<li>
				<div class="flex items-center justify-center gap-5 align-middle">
					<span><img src={team.logo} alt={team.name} class="w-8 h-8 rounded-full" /></span>
					<span>{team.name}</span>
				</div>
			</li>
		{/each}
	</ul>

	<!-- call the component itself passing as argument half the array 
	this means the component renders a series of <ul> elements until a winner is described in the <h2> element
	-->
	<svelte:self bracket={selectWinners(bracket)} />
{/if}

<style>
	/* to align the names vertically, stretch the list items in a flex-column  */
	ul {
		display: flex;
		flex-direction: column;
		/* flex grow to have the lists stretch and cover the available width (same as the heading) */
		flex-grow: 1;
		list-style: none;
	}
	ul li,
	h2 {
		flex-grow: 1;
		display: flex;
		flex-direction: column;
		justify-content: center;
		padding: 1rem 0.5rem;
		text-align: center;
	}
	ul li {
		position: relative;
		font-size: 0.95rem;
	}
	/* with pseudo elements draw the following structure next to each couplet
	---
		|
		|
	---
	
	this by drawing half the structure on the even items and the mirrored tructure on the odd ones
	*/
	ul li:before,
	ul li:after {
		position: absolute;
		content: '';
		top: 50%;
		left: 100%;
		background: currentColor;
	}
	ul li:before {
		width: 1rem;
		height: 2px;
		transform: translate(-100%, -50%);
	}
	ul li:after {
		width: 2.5px;
		border-radius: 0 0.625rem 0.625rem 0;
		height: 100%;
	}
	ul:nth-of-type(n) li:nth-of-type(odd):after {
		transform: translate(0%, 0%);
	}
	ul:nth-of-type(n) li:nth-of-type(even):after {
		transform: translate(0%, -100%);
	}
	h2 {
		text-transform: uppercase;
		font-size: 1.25rem;
	}
</style>
