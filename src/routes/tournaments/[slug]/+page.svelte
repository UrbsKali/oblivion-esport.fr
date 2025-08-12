<script>
	// get url slug from router
	import { page } from '$app/stores';
	import { supabase } from '$lib/supabaseClient';
	import { onMount } from 'svelte';

	import { userdata } from '$lib/store';

	import SvelteMarkdown from 'svelte-markdown';
	import { renderers } from '$lib/renderers';

	import RegisterButton from '$lib/components/others/RegisterButton.svelte';
	import Footer from '$lib/components/share/Footer.svelte';
	import DoubleBracket from '$lib/components/others/DoubleBracket.svelte';
	import Pool from '$lib/components/others/Pool.svelte';
	import MatchCard from '$lib/components/others/MatchCard.svelte';

	let slug = '';
	let tournament = {};
	let user;
	let isMounted = false;

	let matchs = [];
	let match_by_day = {};

	let pools = [];
	let play_offs = [];

	let current_body = '';
	let buttons = ['Infos', 'Inscriptions', 'Règlement'];
	let current_button = 'Infos';

	// Bracket builder helpers
	const NA_LOGO = '/assets/oblivion.png';
	function placeholderTeam(date = null) {
		return { name: 'N/A', score: ' ', logo: NA_LOGO, date };
	}
	function parsePhaseKey(phase) {
		// accept both 'bracket' and common typo 'backet'
		if (!phase) return null;
		const m = String(phase).match(/^(?:bracket|backet)-(w|l)-p(\d+)-g(\d+)$/i);
		if (!m) return null;
		return { side: m[1].toLowerCase(), p: Number(m[2]), g: Number(m[3]) };
	}
	function extractScores(scoreText) {
		// scoreText like "2-1"
		const m = String(scoreText || '').match(/^(\d+)\s*-\s*(\d+)$/);
		if (!m) return { a: ' ', b: ' ' };
		return { a: m[1], b: m[2] };
	}
	function buildBracketFromMatches(matches) {
		// Group by phase number p and side (w/l)
		const byPhase = new Map(); // p -> { w: [], l: [] }
		let hasLoser = false;
		for (const m of matches) {
			const meta = parsePhaseKey(m.phase);
			if (!meta) continue;
			if (!byPhase.has(meta.p)) byPhase.set(meta.p, { w: [], l: [] });
			byPhase.get(meta.p)[meta.side].push({ ...m, __meta: meta });
			if (meta.side === 'l') hasLoser = true;
		}

		const phaseNumbers = [...byPhase.keys()].sort((a, b) => a - b);
		const totalRounds = phaseNumbers.length;
		const rounds = [];
		for (let i = 0; i < phaseNumbers.length; i++) {
			const p = phaseNumbers[i];
			const bucket = byPhase.get(p);
			// sort by group index
			bucket.w.sort((a, b) => a.__meta.g - b.__meta.g);
			bucket.l.sort((a, b) => a.__meta.g - b.__meta.g);

			const winner = bucket.w.map((mm) => {
				const { a, b } = extractScores(mm.score);
				const wtag = mm?.winner?.tag;
				let s1 = a;
				let s2 = b;
				if (wtag && mm?.team_one?.tag && mm?.team_two?.tag) {
					if (wtag === mm.team_one.tag) {
						// a->one, b->two
					} else if (wtag === mm.team_two.tag) {
						[s1, s2] = [b, a];
					}
				}
				const baseMatch = {
					score: `${s1}-${s2}`,
					date: mm.date,
					winner: wtag ? { tag: wtag } : null,
					team_one: mm?.team_one?.tag ? { tag: mm.team_one.tag } : null,
					team_two: mm?.team_two?.tag ? { tag: mm.team_two.tag } : null
				};
				const t1 = mm.team_one
					? {
							name: mm.team_one.tag || mm.team_one.name,
							score: s1,
							logo: mm.team_one.logo_url || NA_LOGO,
							date: mm.date,
							match: baseMatch
						}
					: placeholderTeam(mm.date);
				const t2 = mm.team_two
					? {
							name: mm.team_two.tag || mm.team_two.name,
							score: s2,
							logo: mm.team_two.logo_url || NA_LOGO,
							date: mm.date,
							match: baseMatch
						}
					: placeholderTeam(mm.date);
				return [t1, t2];
			});

			const loserArr = bucket.l.map((mm) => {
				const { a, b } = extractScores(mm.score);
				const wtag = mm?.winner?.tag;
				let s1 = a;
				let s2 = b;
				if (wtag && mm?.team_one?.tag && mm?.team_two?.tag) {
					if (wtag === mm.team_one.tag) {
						// a->one, b->two
					} else if (wtag === mm.team_two.tag) {
						[s1, s2] = [b, a];
					}
				}
				const baseMatch = {
					score: `${s1}-${s2}`,
					date: mm.date,
					winner: wtag ? { tag: wtag } : null,
					team_one: mm?.team_one?.tag ? { tag: mm.team_one.tag } : null,
					team_two: mm?.team_two?.tag ? { tag: mm.team_two.tag } : null
				};
				const t1 = mm.team_one
					? {
							name: mm.team_one.tag || mm.team_one.name,
							score: s1,
							logo: mm.team_one.logo_url || NA_LOGO,
							date: mm.date,
							match: baseMatch
						}
					: placeholderTeam(mm.date);
				const t2 = mm.team_two
					? {
							name: mm.team_two.tag || mm.team_two.name,
							score: s2,
							logo: mm.team_two.logo_url || NA_LOGO,
							date: mm.date,
							match: baseMatch
						}
					: placeholderTeam(mm.date);
				return [t1, t2];
			});

			const title =
				i === totalRounds - 1 ? 'Finale' : i === totalRounds - 2 ? 'Demie finale' : `Tour ${i + 1}`;
			const loserTitle = `Manche des perdants ${i + 1}`;

			const roundObj = hasLoser ? { title, loserTitle, winner, loser: loserArr } : { title, loserTitle, winner };

			rounds.push(roundObj);
		}
		return rounds;
	}

	onMount(async () => {
		isMounted = true;
		await loadPage();
	});

	userdata.subscribe((value) => {
		if (value) {
			user = value;
		}
	});

	page.subscribe(async (value) => {
		slug = value.params.slug;
		if (!isMounted) return;
		await loadPage();
	});

	async function loadPage() {
		const { data, error } = await supabase
			.from('Tournaments')
			.select('id, title, start, end, slug(body, image, last_update), can_register')
			.eq('slug', slug)
			.single();

		if (error) {
			console.error('error', error);
		} else {
			tournament = data;

			buttons = Object.keys(tournament?.slug?.body).map((key) => {
				return key;
			});
			// Ensure Play-offs tab exists (auto from DB)
			if (!buttons.includes('Play-offs')) buttons = [...buttons, 'Play-offs'];
			current_body = tournament?.slug?.body[buttons[0]] || '';
			current_button = buttons[0];
		}
	}

	async function loadMatchs(batch = 0) {
		const { data, error } = await supabase
			.from('Matchs')
			.select(
				'id, team_one(logo_url, name, tag), team_two(logo_url, name, tag), winner(tag), date, score'
			)
			.eq('tournament_id', tournament?.id)
			.order('date', { ascending: true })
			.range(batch * 10, (batch + 1) * 10);

		if (error) {
			console.error('error', error);
		} else {
			matchs = [...matchs, ...data];
			match_by_day = matchs.reduce((acc, match) => {
				const date = new Date(match.date).toDateString();
				if (!acc[date]) {
					acc[date] = [];
				}
				acc[date].push(match);
				return acc;
			}, {});
			console.log(match_by_day);
		}
	}

	async function loadPool() {
		const { data, error } = await supabase
			.from('Matchs')
			.select('id, team_one(id,tag), team_two(id,tag), winner!inner(id,tag), date, score')
			.eq('tournament_id', tournament?.id)
			.eq('phase', 'group');
		if (error) {
			console.error('error', error);
		} else {
			// get teams win and nb matchs
			let teams = {};
			data.forEach((el) => {
				if (!teams[el.team_one.tag]) {
					teams[el.team_one.tag] = {
						win: 0,
						match: 0
					};
				}
				if (!teams[el.team_two.tag]) {
					teams[el.team_two.tag] = {
						win: 0,
						match: 0
					};
				}

				teams[el.team_one.tag].match++;
				teams[el.team_two.tag].match++;

				teams[el.winner.tag].win++;
			});

			current_body.forEach((el) => {
				el.teams.map((team) => {
					team.win = teams[team.name]?.win || 0;
					team.match = teams[team.name]?.match || 0;
				});
			});

			pools = current_body;
		}
	}

	async function loadBracket() {
		if (!tournament?.id) return;
		const { data, error } = await supabase
			.from('Matchs')
			.select(
				'id, team_one(logo_url, name, tag), team_two(logo_url, name, tag), date, score, phase, winner(tag)'
			)
			.eq('tournament_id', tournament?.id)
			.or('phase.ilike.bracket-%,phase.ilike.backet-%')
			.order('phase', { ascending: true });

		if (error) {
			console.error('error', error);
			play_offs = [];
			return;
		}
		play_offs = buildBracketFromMatches(data || []);
		console.log('Bracket loaded:', play_offs);
	}

	// let bracket = [
	// 	{
	// 		id: 1,
	// 		name: 'KMF',
	// 		logo: 'https://idlcqblimgotmibuednf.supabase.co/storage/v1/object/public/avatars/1846c911-521b-4338-85c5-835132e1765e/teams_r9dcj7llev9q1ynlttyxd.png'
	// 	},
	// 	{
	// 		id: 2,
	// 		name: 'NSE',
	// 		logo: 'https://idlcqblimgotmibuednf.supabase.co/storage/v1/object/public/avatars/3799ee3f-255b-4aa8-8a86-cbc1bd66ee71/teams_1f6mfk5vkshix6qwqwgme.png'
	// 	},
	// 	{
	// 		id: 3,
	// 		name: 'FAS',
	// 		logo: 'https://idlcqblimgotmibuednf.supabase.co/storage/v1/object/public/avatars/e7c2b0e7-f866-4e98-8c15-1e84918fe668/teams_mt27ui9z6hqs8sa1le7lf8.png'
	// 	},
	// 	{
	// 		id: 4,
	// 		name: 'GST',
	// 		logo: 'https://idlcqblimgotmibuednf.supabase.co/storage/v1/object/public/avatars/5ba7937a-1670-49c1-ae18-7dc4c2f588de/teams_ys0vjs1mu8jjz87h9q38.png'
	// 	},
	// 	{
	// 		id: 5,
	// 		name: 'BZZ',
	// 		logo: 'https://idlcqblimgotmibuednf.supabase.co/storage/v1/object/public/avatars/29342981-2bc0-4980-9b12-71fd5487c0b3/teams_jwmdgnihx6o1rchvqpzcv5.png'
	// 	},
	// 	{
	// 		id: 6,
	// 		name: 'WDE',
	// 		logo: 'https://idlcqblimgotmibuednf.supabase.co/storage/v1/object/public/avatars/1850792e-7425-462d-9c14-0cc8e73cf73a/teams_w4irqztd5is726f7n6tun9.png'
	// 	},
	// 	{
	// 		id: 7,
	// 		name: 'NMS',
	// 		logo: 'https://idlcqblimgotmibuednf.supabase.co/storage/v1/object/public/avatars/97e6631d-cef9-4614-9b3c-27f6674a59b5/teams_3bsy4jg8cn2wvvi1549gu.png'
	// 	},
	// 	{
	// 		id: 8,
	// 		name: 'ICA',
	// 		logo: 'https://idlcqblimgotmibuednf.supabase.co/storage/v1/object/public/avatars/edff3703-0ac5-4af5-bfa4-216750cc2b2c/teams_671gl7tdzuhsoqytfztbp.jpg'
	// 	}
	// ];
</script>

<svelte:head>
	<title>{tournament?.title || slug} - Obivion Esport</title>
	<meta name="description" content="Tournoi {tournament?.title || slug} sur Obivion Esport" />
	<meta property="og:title" content="{tournament?.title || slug} - Obivion Esport" />
	<meta
		property="og:description"
		content="Tournoi {tournament?.title || slug} sur Obivion Esport"
	/>
	<meta property="og:image" content={tournament?.slug?.image} />
	<meta property="og:url" content={`https://obivion.gg/tournaments/${slug}`} />
	<meta property="og:type" content="website" />
	<meta property="og:site_name" content="Obivion Esport" />
	<meta name="twitter:card" content="summary_large_image" />
	<meta name="twitter:site" content="@ObivionEsport" />
	<meta name="twitter:creator" content="@ObivionEsport" />
	<meta name="twitter:title" content="{tournament?.title || slug} - Obivion Esport" />
	<meta
		name="twitter:description"
		content="Tournoi {tournament?.title || slug} sur Obivion Esport"
	/>
	<meta name="twitter:image" content={tournament?.slug?.image} />
	<meta name="twitter:url" content={`https://obivion.gg/tournaments/${slug}`} />
</svelte:head>

<div class="min-h-screen">
	<div
		id="hero"
		style="background-image: linear-gradient(0deg, rgba(14, 19, 31, 1) 0%, rgba(14, 19,31, 0.5) 100%), url({tournament
			?.slug?.image});"
		class="relative h-screen bg-center bg-no-repeat bg-cover"
	></div>

	<div
		class="container flex flex-col mt-[-60vh] items-center justify-center px-4 align-middle md:mx-auto relative w-full md:w-10/12"
	>
		<div class="w-full py-5">
			<h1 class="text-4xl font-bold">{tournament?.title || slug}</h1>
			<p class="text-lg text-gray-400">// {tournament?.start || ''} - {tournament?.end || ''}</p>
		</div>
		<div class="items-start w-full">
			<!-- tab system for the glassmorphism card below -->
			<div class="flex flex-wrap justify-start">
				{#each buttons as button, index}
					<button
						class="px-4 py-2 text-white transition-all border border-gray-700 {index == 0
							? 'rounded-ss-lg'
							: ''}
							{index == buttons.length - 1
							? 'rounded-se-lg'
							: ' '} bg-opacity-10 backdrop-blur-lg hover:bg-gray-700"
						on:click={() => {
							current_body = tournament?.slug?.body[button];
							current_button = button;
							if (button == 'Matchs') {
								matchs = [];
								loadMatchs();
							}
							if (button == 'Phase de groupe') {
								pools = [];
								loadPool();
							}
							if (button == 'Play-offs') {
								play_offs = [];
								loadBracket();
							}
						}}
					>
						{button}
					</button>
				{/each}
			</div>
		</div>
		<div class="w-full p-5 border border-gray-700 rounded-b-lg rounded-e-lg backdrop-blur-lg">
			{#if current_button == 'Play-offs'}
				<div class="flex h-[1050px]">
					<!-- Make the render wait until play_offs is loaded -->
					{#if play_offs.length === 0}
						<p class="text-gray-500">Chargement des phases finales...</p>
					{:else}
						<DoubleBracket bracket={play_offs} />
					{/if}
				</div>
			{:else if current_button == 'Inscriptions'}
				<SvelteMarkdown source={current_body} {renderers} />

				{#if tournament?.can_register}
					<RegisterButton tournament_id={tournament?.id} />
				{:else}
					<button class="px-4 py-2 mt-5 text-white bg-gray-500 rounded-md"
						>Inscription fermée</button
					>
				{/if}
			{:else if current_button == 'Matchs'}
				<div class="flex flex-col gap-8">
					{#each Object.keys(match_by_day) as date}
						{@const matchs = match_by_day[date]}
						{@const dt = new Date(date)}

						<h1 class="text-2xl font-extrabold capitalize">
							{dt.toLocaleDateString('fr-FR', {
								weekday: 'long',
								year: 'numeric',
								month: 'long',
								day: 'numeric'
							})}
						</h1>
						<div class="grid grid-cols-1 gap-5 lg:grid-cols-3 md:grid-cols-2">
							{#each matchs as m}
								<MatchCard
									name={[m.team_one.name, m.team_two.name]}
									logo={[m.team_one.logo_url, m.team_two.logo_url]}
									match={m}
								/>
							{/each}
						</div>
					{/each}
					<!-- Charger plus -->
					<button
						class="px-4 py-2 text-white bg-gray-700 rounded-md"
						on:click={() => loadMatchs(matchs.length / 10)}
					>
						Charger plus
					</button>
				</div>
			{:else if current_button == 'Phase de groupe'}
				<div class="flex flex-col w-full gap-5 justify-evenly md:flex-row">
					{#each pools as pool}
						<Pool {pool} />
					{/each}
				</div>
			{:else}
				<SvelteMarkdown source={current_body} {renderers} />
			{/if}
		</div>
	</div>
</div>
<div class="mt-10">
	<Footer />
</div>
