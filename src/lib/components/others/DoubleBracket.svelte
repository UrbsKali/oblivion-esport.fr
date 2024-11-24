<script>
	import MatchCard from './MatchCard.svelte';

	export let bracket = [
		{
			title: 'Tour 1',
			loserTitle: 'Manche des perdants 1',
			winner: [
				[
					{
						name: 'NMS',
						score: ' ',
						logo: 'https://idlcqblimgotmibuednf.supabase.co/storage/v1/object/public/avatars/97e6631d-cef9-4614-9b3c-27f6674a59b5/teams_3bsy4jg8cn2wvvi1549gu.png'
					},
					{
						name: 'TMP',
						score: ' ',
						logo: 'https://idlcqblimgotmibuednf.supabase.co/storage/v1/object/public/avatars/97e6631d-cef9-4614-9b3c-27f6674a59b5/teams_3bsy4jg8cn2wvvi1549gu.png'
					}
				],
				[
					{
						name: 'ICA',
						score: ' ',
						logo: 'https://idlcqblimgotmibuednf.supabase.co/storage/v1/object/public/avatars/edff3703-0ac5-4af5-bfa4-216750cc2b2c/teams_671gl7tdzuhsoqytfztbp.jpg'
					},
					{
						name: 'WDE',
						score: ' ',
						logo: 'https://idlcqblimgotmibuednf.supabase.co/storage/v1/object/public/avatars/1850792e-7425-462d-9c14-0cc8e73cf73a/teams_w4irqztd5is726f7n6tun9.png'
					}
				],
				[
					{
						name: 'OMG',
						score: ' ',
						logo: 'https://idlcqblimgotmibuednf.supabase.co/storage/v1/object/public/avatars/98a0a90a-43a2-48c7-b4e7-c39f1c42d183/teams_exdnmmbfqv6bwnyyxtiuj5.png'
					},
					{
						name: 'GST',
						score: ' ',
						logo: 'https://idlcqblimgotmibuednf.supabase.co/storage/v1/object/public/avatars/5ba7937a-1670-49c1-ae18-7dc4c2f588de/teams_ys0vjs1mu8jjz87h9q38.png'
					}
				],
				[
					{
						name: 'EVYL',
						score: ' ',
						logo: 'https://idlcqblimgotmibuednf.supabase.co/storage/v1/object/public/avatars/a7c0f3d3-9ab0-48da-9b19-d897bf5a350b/teams_z772kf6erx7nfnrzn0kvl.png'
					},
					{
						name: 'KMF',
						score: ' ',
						logo: 'https://idlcqblimgotmibuednf.supabase.co/storage/v1/object/public/avatars/1846c911-521b-4338-85c5-835132e1765e/teams_r9dcj7llev9q1ynlttyxd.png'
					}
				]
			],
			loser: [
				[
					{
						name: 'N/A',
						score: ' ',
						logo: '/assets/oblivion.png'
					},
					{
						name: 'N/A',
						score: ' ',
						logo: '/assets/oblivion.png'
					}
				],
				[
					{
						name: 'N/A',
						score: ' ',
						logo: '/assets/oblivion.png'
					},
					{
						name: 'N/A',
						score: ' ',
						logo: '/assets/oblivion.png'
					}
				]
			]
		},
		{
			title: 'Tour 2',
			loserTitle: 'Manche des perdants 2',
			winner: [
				[
					{
						name: 'N/A',
						score: ' ',
						logo: '/assets/oblivion.png'
					},
					{
						name: 'N/A',
						score: ' ',
						logo: '/assets/oblivion.png'
					}
				],
				[
					{
						name: 'N/A',
						score: ' ',
						logo: '/assets/oblivion.png'
					},
					{
						name: 'N/A',
						score: ' ',
						logo: '/assets/oblivion.png'
					}
				]
			],
			loser: [
				[
					{
						name: 'N/A',
						score: ' ',
						logo: '/assets/oblivion.png'
					},
					{
						name: 'N/A',
						score: ' ',
						logo: '/assets/oblivion.png'
					}
				],
				[
					{
						name: 'N/A',
						score: ' ',
						logo: '/assets/oblivion.png'
					},
					{
						name: 'N/A',
						score: ' ',
						logo: '/assets/oblivion.png'
					}
				]
			]
		},
		{
			title: 'Demie finale',
			loserTitle: 'Manche des perdants 3',
			winner: [
				[
					{
						name: 'N/A',
						score: ' ',
						logo: '/assets/oblivion.png'
					},
					{
						name: 'N/A',
						score: ' ',
						logo: '/assets/oblivion.png'
					}
				]
			],
			loser: [
				[
					{
						name: 'N/A',
						score: ' ',
						logo: '/assets/oblivion.png'
					},
					{
						name: 'N/A',
						score: ' ',
						logo: '/assets/oblivion.png'
					}
				]
			]
		},
		{
			title: 'Finale',
			loserTitle: 'Manche des perdants 4',
			winner: [
				[
					{
						name: 'N/A',
						score: ' ',
						logo: '/assets/oblivion.png'
					},
					{
						name: 'N/A',
						score: ' ',
						logo: '/assets/oblivion.png'
					}
				]
			],
			loser: [
				[
					{
						name: 'N/A',
						score: ' ',
						logo: '/assets/oblivion.png'
					},
					{
						name: 'N/A',
						score: ' ',
						logo: '/assets/oblivion.png'
					}
				]
			]
		}
	];
</script>

<div class="grid w-full h-full grid-flow-col grid-cols-4 grid-rows-2 gap-5 min-w-[600px]">
	{#each bracket as round, index}
		<div class="grid h-full text-center">
			<h2>{round.title}</h2>
			<div class="flex flex-col gap-5">
				{#each round.winner as matchRaw, i}
					{@const match = {
						team_one: { tag: matchRaw[0]?.name, score: matchRaw[0]?.score },
						team_two: { tag: matchRaw[1]?.name, score: matchRaw[1]?.score },
						winner: matchRaw[0]?.score > matchRaw[1]?.score ? matchRaw[0]?.name : matchRaw[1]?.name,
						score: `${matchRaw[0]?.score} - ${matchRaw[1]?.score}`
					}}
					{@const name = [matchRaw[0]?.name, matchRaw[1]?.name]}
					{@const logo = [matchRaw[0]?.logo, matchRaw[1]?.logo]}
					{#if index != 0}
						<div class="flex flex-col justify-center h-[250px]">
							<MatchCard {match} {name} {logo} showTime={false} />
						</div>
					{:else}
						<MatchCard {match} {name} {logo} showTime={false} />
					{/if}
				{/each}
			</div>
		</div>
		<div class="flex flex-col h-full gap-5 text-center">
			<h2>{round.loserTitle}</h2>
			<div class="flex flex-col gap-5">
				{#each round.loser as matchRaw, i}
					{@const match = {
						team_one: { tag: matchRaw[0]?.name, score: matchRaw[0]?.score },
						team_two: { tag: matchRaw[1]?.name, score: matchRaw[1]?.score },
						winner: matchRaw[0]?.score > matchRaw[1]?.score ? matchRaw[0]?.name : matchRaw[1]?.name,
						score: `${matchRaw[0]?.score} - ${matchRaw[1]?.score}`
					}}
					{@const name = [matchRaw[0]?.name, matchRaw[1]?.name]}
					{@const logo = [matchRaw[0]?.logo, matchRaw[1]?.logo]}
					<MatchCard {match} {name} {logo} showTime={false} />
				{/each}
			</div>
		</div>
	{/each}
</div>
