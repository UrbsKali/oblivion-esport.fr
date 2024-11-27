<script>
	import { writable } from 'svelte/store';
	import { SvelteFlow, SvelteFlowProvider, useSvelteFlow } from '@xyflow/svelte';

	// 👇 this is important! You need to import the styles for Svelte Flow to work
	import '@xyflow/svelte/dist/style.css';
	import MatchNode from '$lib/components/flowNodes/MatchNode.svelte';
	import HeaderNode from '../flowNodes/HeaderNode.svelte';
	import { onMount } from 'svelte';

	const { fitView, viewport, setCenter } = useSvelteFlow();

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
						name: 'SKY',
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

	let isMobile = true;

	const customNodes = {
		match: MatchNode,
		title: HeaderNode
	};

	let nodes = writable([]);
	let edges = writable([]);

	function parseBraket(bracket) {
		let nodes_ = [];
		let edges_ = [];

		let currentId = 1;

		let middle = (bracket[0].winner.length * 150) / 2;
		bracket.forEach((el, i) => {
			nodes_.push({
				id: `${i}-TitleWinner`,
				position: { x: 300 * (i + 1), y: -50 },
				type: 'title',
				draggable: false,
				dragHandle: false,
				selectable: false,
				data: {
					text: el.title
				}
			});
			nodes_.push({
				id: `${i}-TitleLoser`,
				position: { x: 300 * (i + 1), y: 625 },
				type: 'title',
				draggable: false,
				dragHandle: false,
				selectable: false,
				data: {
					text: el.loserTitle
				}
			});
			el.winner.forEach((win_el, j) => {
				let y = 150 * (j + 0);
				console.log(win_el[0].date);
				if (i != 0) y += middle - (el.winner.length * 150) / 2;
				nodes_ = [
					...nodes_,
					{
						id: `${i}-${j}`,
						position: { x: 300 * (i + 1), y },
						type: 'match',
						draggable: false,
						dragHandle: false,
						selectable: false,
						data: {
							name: [win_el[0].name, win_el[1].name],
							logo: [win_el[0].logo, win_el[1].logo],
							match: {
								score: `${win_el[0].score}-${win_el[0].score}`,
								date: win_el[0].date
							},
							showTime: win_el[0].date ? true : false,
							nb: currentId++
						}
					}
				];
			});
			el.loser.forEach((los_el, j) => {
				nodes_ = [
					...nodes_,
					{
						id: `${i}-${j}-loser${los_el[0].connectOne ? '@' : ''}`,
						position: { x: 300 * (i + 1), y: 150 * (j + 0) + 700 },
						type: 'match',
						draggable: false,
						dragHandle: false,
						selectable: false,
						data: {
							name: [los_el[0].name, los_el[1].name],
							logo: [los_el[0].logo, los_el[1].logo],
							match: {
								score: `${los_el[0].score}-${los_el[0].score}`,
								date: los_el[0].date
							},
							showTime: los_el[0].date ? true : false,

							nb: currentId++
						}
					}
				];
			});
		});

		for (let i in nodes_) {
			console.log(nodes_[i]);
			const el = nodes_[i];
			let edge = {
				id: `${el.id}-edge`,
				type: 'smoothstep',
				source: el.id,
				target: `${parseInt(el.id.split('-')[0]) + 1}-${Math.floor(el.id.split('-')[1] / 2)}${el.id.split('-')[2] ? '-loser' : ''}`
			};
			if (el.id.split('-')[0] == bracket.length - 1) continue;
			if (el.id.includes('@')) {
				edge.target = `${parseInt(el.id.split('-')[0]) + 1}-${Math.floor(el.id.split('-')[1])}-loser`;
			}
			edges_ = [...edges_, edge];
		}

		nodes.set(nodes_);
		edges.set(edges_);
	}

	$: parseBraket(bracket);

	onMount(() => {
		isMobile = window.innerWidth < 1500;
		console.log($viewport);
		if (!isMobile) {
			fitView();
			setTimeout(() => {
				fitView();
			}, 50);
		}
	});
</script>

<SvelteFlow
	{nodes}
	{edges}
	initialViewport={{ x: -290, y: 50, zoom: 0.99 }}
	nodeTypes={customNodes}
	colorMode="dark"
	nodesDraggable={false}
	elementsSelectable={false}
	nodesConnectable={false}
	zoomOnScroll={false}
	zoomOnPinch={false}
	zoomOnDoubleClick={false}
	preventScrolling={false}
	panOnScroll={true}
	panOnScrollMode="horizontal"
	translateExtent={[
		[250, -50],
		[1500, 1000]
	]}
	style="background-color: rgba(0,0,0,0)"
></SvelteFlow>

<style>
	:global(.svelte-flow .svelte-flow__handle) {
		width: 0px;
		height: 0px;
		z-index: -1;
	}

	:global(.svelte-flow .svelte-flow__handle-left) {
		transform: translateX(-5px);
		background-color: #00000000;
	}
	:global(.svelte-flow .svelte-flow__handle-right) {
		transform: translateX(-1px);
		background-color: #00000000;
	}
	:global(.svelte-flow__attribution) {
		display: none;
	}
</style>
