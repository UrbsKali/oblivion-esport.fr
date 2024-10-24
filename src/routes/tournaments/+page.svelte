<script>
	import Cursor from '$lib/components/share/Cursor.svelte';
	import Footer from '$lib/components/share/Footer.svelte';
	import { onMount } from 'svelte';

	let items = [
		{
			title: 'ELEON WORLD CUP',
			content: 'Description for event 1',
			date: '2024-11-24',
			slug: 'eleon-world-cup'
		},
		{
			title: 'Trankil World Cup 5',
			content: 'Description for event 2',
			date: '2024-08-01',
			slug: 'trankil-world-cup-5'
		},
		{
			title: 'Trankil Invitational',
			content: 'Description for event 3',
			date: '2023-12-12',
			slug: 'trankil-invitational'
		},
		{
			title: 'Trankil World Cup 4',
			content: 'Description for event 4',
			date: '2023-08-01',
			slug: 'trankil-world-cup-4'
		},
		{
			title: 'Trankil World Cup 3',
			content: 'Description for event 5',
			date: '2022-08-01',
			slug: 'trankil-world-cup-3'
		},
		{
			title: 'Trankil World Cup 2',
			content: 'Description for event 6',
			date: '2021-08-01',
			slug: 'trankil-world-cup-2'
		},
		{
			title: 'Trankil World Cup 1',
			content: 'Description for event 7',
			date: '2020-08-01',
			slug: 'trankil-world-cup-1'
		}
	];

	let currentPercentage = 0;
	let scrollPercentage = 0;
	let oblivion;
	let timelineHeight = 0;
	let lastPercentage = 0;
	let t = 0;
	const FPS = 30;

	let cups_positions = [];

	onMount(() => {
		// add the middle x and y coordinates for each .cup element
		const cups = document.querySelectorAll('.cup');
		cups.forEach((cup) => {
			const rect = cup.getBoundingClientRect();
			cup.dataset.middleX = rect.left + rect.width / 2;
			cup.dataset.middleY = rect.top + rect.height / 2;
			cups_positions.push(rect.top + rect.height / 2);
		});
		oblivion = document.getElementById('oblivon');
		let middle = window.innerWidth / 2;
		oblivion.style.left = middle - 24 + 'px';

		const timeline = document.getElementById('timeline');
		const timelineRect = timeline.getBoundingClientRect();
		timelineHeight = timelineRect.height;
		console.log(timelineHeight);

		window.addEventListener('scroll', () => {
			scrollPercentage = window.scrollY / (document.body.scrollHeight - window.innerHeight);
		});
		setInterval(() => {
			requestAnimationFrame(animateOblivon);
		}, 1000 / FPS);
	});

	function animateOblivon() {
		currentPercentage += (scrollPercentage - currentPercentage) * 0.1;
		lastPercentage = currentPercentage;
		let min = 0.975;
		oblivion.style.top =
			currentPercentage * timelineHeight * lerp(1.5, min, 1 - Math.exp(-currentPercentage * 3)) +
			lerp(72, 0, currentPercentage) +
			'px';

		// if the oblivion obj is close to a cup, make it rotate 360 degrees
		const closest = cups_positions.reduce((prev, curr) => {
			return Math.abs(curr - currentPercentage * timelineHeight) <
				Math.abs(prev - currentPercentage * timelineHeight)
				? curr
				: prev;
		});
		if (Math.abs(closest - currentPercentage * timelineHeight) < 50) {
			oblivion.style.transform = 'rotate(360deg)';
		} else {
			oblivion.style.transform = 'rotate(0deg)';
		}

		// make it hover if not scrolling
		if (Math.abs(lastPercentage - currentPercentage) < 0.01) {
			oblivion.style.transform = 'rotate(0deg) translateY(' + Math.sin(t) * 10 + 'px)';
			t += 0.025;
			if (t > Math.PI * 2) {
				t = 0;
				oblivion.style.transform = 'rotate(360deg)';
			}
		}
	}
	function lerp(start, end, t) {
		return start * (1 - t) + end * t;
	}
</script>

<img id="oblivon" class="absolute z-20 hidden sm:block" src="/v2/oblivion.png" />
<Cursor />
<div class="enable-cursor">
	<div class="container py-8 mx-auto">
		<div class="relative h-full p-10 overflow-hidden wrap">
			<div class="absolute h-full border-gray-700 md:border border-2-2" id="timeline"></div>
			{#each items as item, index}
				{#if index % 2 === 0}
					<div
						class="flex flex-col items-center justify-center m-2 bg-cover md:flex-row cup"
						style="background-image: linear-gradient(
				110deg,
				rgb(14, 19, 31, 1) 30%,
				rgb(14, 19, 31, 0) 55%,
				rgb(14, 19, 31, 1) 90%
			),
			linear-gradient(0deg, rgb(14, 19, 31, 1) 10%, rgb(14, 19, 31, 0) 50%, rgb(14, 19, 31, 1) 90%),
			linear-gradient(90deg, rgb(14, 19, 31, 1) 0%, rgb(14, 19, 31, 0) 2%, rgb(14, 19, 31, 0) 98%, rgb(14, 19, 31, 1) 100%),
			url('https://placecats.com/350/200');"
					>
						<div
							class="flex flex-col items-center justify-center p-5 bg-gray-900 bg-opacity-25 border border-gray-700 rounded-lg lg:p-10 backdrop-blur-lg"
						>
							<div class="flex flex-col items-center justify-center w-9/12 h-full">
								<div class="">
									<h1 class="text-4xl font-bold text-gray-300 w-max">{item.title}</h1>
									<span class="text-lg text-left text-gray-500">// {item.date}</span>
									<p class="text-lg text-gray-300">{item.content}</p>
									<div>
										<a
											class="px-4 py-2 mt-5 text-white border rounded-md border-primary-500"
											href="/v2/tournaments/">En savoir +</a
										>
										<button class="px-4 py-2 mt-5 text-white rounded-md bg-primary-500"
											>Inscription</button
										>
									</div>
								</div>
							</div>
						</div>
						<div class="hidden w-6/12 md:block aspect-square min-w-96" id="rl"></div>
					</div>
				{:else}
					<div
						class="flex flex-col items-center justify-center m-2 bg-cover md:flex-row cup"
						style="background-image: linear-gradient(
		70deg,
		rgb(14, 19, 31, 1) 10%,
		rgb(14, 19, 31, 0) 45%,
		rgb(14, 19, 31, 1) 70%
	),
	linear-gradient(0deg, rgb(14, 19, 31, 1) 10%, rgb(14, 19, 31, 0) 50%, rgb(14, 19, 31, 1) 90%),
	linear-gradient(90deg, rgb(14, 19, 31, 1) 0%, rgb(14, 19, 31, 0) 2%, rgb(14, 19, 31, 0) 98%, rgb(14, 19, 31, 1) 100%),
	url('https://placecats.com/350/200');"
					>
						<div class="hidden w-6/12 md:block aspect-square min-w-96" id="rl"></div>
						<div
							class="flex flex-col items-center justify-center p-5 bg-gray-900 bg-opacity-25 border border-gray-700 rounded-lg lg:p-10 backdrop-blur-lg"
						>
							<div class="flex flex-col items-center justify-center w-9/12 h-full">
								<div class="text-right">
									<h1 class="text-4xl font-bold text-gray-300 w-max">{item.title}</h1>
									<span class="text-lg text-left text-gray-500">{item.date} //</span>
									<p class="text-lg text-gray-300">{item.content}</p>
									<div>
										<a
											class="px-4 py-2 mt-5 text-white border rounded-md border-primary-500"
											href="/v2/tournaments/">En savoir +</a
										>
										<button class="px-4 py-2 mt-5 text-white rounded-md bg-primary-500"
											>Inscription</button
										>
									</div>
								</div>
							</div>
						</div>
					</div>
				{/if}
			{/each}
		</div>
	</div>
</div>
<Footer />

<style>
	.cup::after {
		content: '';
		position: absolute;
		width: 42px;
		aspect-ratio: 1;
		border-radius: 50%;
		border-color: #374151;
		backdrop-filter: blur(12px);
		border-width: 2px;
		left: calc(50% - 21px);
		top: data(middleY);
	}
	@media (max-width: 768px) {
		.cup::after {
			content: none !important;
		}
	}

	#timeline {
		left: calc(50% - 1px);
		top: 40px;
	}
	#oblivon {
		width: 33px;
		height: 33px;
		transition: all 0.1s ease-out;
	}
</style>
