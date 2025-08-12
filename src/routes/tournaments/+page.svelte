<script>
	import Cursor from '$lib/components/share/Cursor.svelte';
	import Footer from '$lib/components/share/Footer.svelte';
	import RegisterButton from '$lib/components/others/RegisterButton.svelte';
	import { onMount } from 'svelte';
	import { supabase } from '$lib/supabaseClient';

	let items = [
		{
			title: 'ELEON WORLD CUP',
			slug: {
				slug: 'eleon-world-cup',
				description: 'Description for event 1',
				image:
					'https://idlcqblimgotmibuednf.supabase.co/storage/v1/object/public/articles/eleon-world-cup/main.png'
			},
			start: '2024-11-24'
		}
	];

	async function loadTournaments() {
		const { data, error } = await supabase
			.from('Tournaments')
			.select('start, title, slug(slug, description, image), can_register')
			.order('start', { ascending: false });
		if (error) {
			console.error('error', error);
		} else {
			items = data;
		}
	}

	let currentPercentage = 0;
	let scrollPercentage = 0;
	let oblivion;
	let timelineHeight = 0;
	let lastPercentage = 0;
	let t = 0;
	const FPS = 30;
	let rotation = 0;

	let isMobile = false;

	let cups_positions = [];

	onMount(async () => {
		isMobile = window.innerWidth < 1024;
		await loadTournaments();

		if (isMobile) {
			return;
		}

		oblivion = document.getElementById('oblivion');
		let middle = window.innerWidth / 2;
		oblivion.style.left = middle - 25 + 'px';

		// add the middle x and y coordinates for each .cup element
		const cups = document.querySelectorAll('.cup');
		cups.forEach((cup) => {
			const rect = cup.getBoundingClientRect();
			cup.dataset.middleX = rect.left + rect.width / 2;
			cup.dataset.middleY = rect.top + rect.height / 2;
			cups_positions.push(rect.top + rect.height / 2 - 16);
		});

		const timeline = document.getElementById('timeline');
		const timelineRect = timeline.getBoundingClientRect();
		timelineHeight = timelineRect.height;

		window.addEventListener('scroll', () => {
			scrollPercentage = window.scrollY / (document.body.scrollHeight - window.innerHeight);
		});
		setInterval(() => {
			requestAnimationFrame(animateOblivon);
		}, 1000 / FPS);
	});

	/**
	 * Bezier curve function
	 * @param {Array} points - Array of control points that define the curve
	 * @param {Number} t - Time
	 * @returns {Number} - the x coordinate on the curve at a given time t
	 */
	function bezierCurve(points, t) {
		if (points.length === 1) {
			return points[0];
		}
		let newPoints = [];
		for (let i = 0; i < points.length - 1; i++) {
			newPoints.push(points[i] + (points[i + 1] - points[i]) * t);
		}
		return bezierCurve(newPoints, t);
	}

	const bezierCurvePoints = [0, 0.25, 0.5, 0.75, 1];

	function animateOblivon() {
		currentPercentage += (scrollPercentage - currentPercentage) * 0.1;
		lastPercentage = currentPercentage;
		let min = 0.975;

		let currentY =
			currentPercentage * timelineHeight * lerp(1.5, min, 1 - Math.exp(-currentPercentage * 3)) +
			lerp(72, 0, currentPercentage);

		let can_hover = true;

		const closest = cups_positions.reduce((prev, curr) => {
			return Math.abs(curr - currentY) < Math.abs(prev - currentY) ? curr : prev;
		});
		if (Math.abs(closest - currentY) < 50) {
			currentY = closest;
			can_hover = false;
			oblivion.classList.add('animate-spin');
		} else {
			oblivion.classList.remove('animate-spin');
		}
		// make it hover if not scrolling
		if (Math.abs(lastPercentage - currentPercentage) < 0.01 && can_hover) {
			currentY += Math.sin(t) * 10;
			t += 0.05;
			if (t > Math.PI * 2) {
				t = 0;
			}
		}

		oblivion.style.top = currentY + 'px';
	}

	function rotateOblivion(reset = false) {
		if (reset) {
			rotation = 0;
		} else {
			rotation += 360;
		}
		oblivion.style.transform = 'rotate(' + rotation + 'deg)';
	}

	function lerp(start, end, t) {
		return start * (1 - t) + end * t;
	}
</script>

<img
	id="oblivion"
	class="absolute z-20 hidden sm:block"
	src="/assets/oblivion.webp"
	alt="moving logo following the scroll on the timeline"
/>
<Cursor />
<div class="enable-cursor">
	<div class="container py-8 mx-auto">
		<div class="relative h-full p-10 overflow-hidden wrap">
			<div class="absolute h-full border-gray-700 md:border border-2-2" id="timeline"></div>
			{#each items as item, index}
				{#if index % 2 === 0 || isMobile}
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
			url('{item.slug.image}');"
					>
						<div
							class="flex flex-col items-center justify-center w-full p-5 bg-gray-900 bg-opacity-25 border border-gray-700 rounded-lg md:w-auto lg:p-10 backdrop-blur-lg"
						>
							<div class="flex flex-col items-center justify-center w-full h-full md:w-9/12">
								<div class="">
									<h1 class="text-4xl font-bold text-gray-300 w-max">{item.title}</h1>
									<span class="text-lg text-left text-gray-500">// {item.start}</span>
									<p class="text-lg text-gray-300">{item.slug.description}</p>
									<div class="mt-5">
										<a
											class="px-4 py-2 mt-5 text-white border rounded-md border-primary-500"
											href="/tournaments/{item.slug.slug}">En savoir +</a
										>
										{#if item.can_register}
											<RegisterButton tournament_id={21} />
										{:else}
											<a
												class="px-4 py-2 mt-5 text-white rounded-md bg-primary-500"
												href="/tournaments/{item.slug.slug}">Voir les résultats</a
											>
										{/if}
									</div>
								</div>
							</div>
						</div>
						<div class="hidden w-6/12 md:block aspect-square min-w-96" id="rl"></div>
					</div>
				{:else}
					<div
						class="flex flex-col items-center justify-center w-full m-2 bg-cover md:flex-row cup"
						style="background-image: linear-gradient(
		70deg,
		rgb(14, 19, 31, 1) 10%,
		rgb(14, 19, 31, 0) 45%,
		rgb(14, 19, 31, 1) 70%
	),
	linear-gradient(0deg, rgb(14, 19, 31, 1) 10%, rgb(14, 19, 31, 0) 50%, rgb(14, 19, 31, 1) 90%),
	linear-gradient(90deg, rgb(14, 19, 31, 1) 0%, rgb(14, 19, 31, 0) 2%, rgb(14, 19, 31, 0) 98%, rgb(14, 19, 31, 1) 100%),
	url('{item.slug.image}');"
					>
						<div class="hidden w-6/12 md:block aspect-square min-w-96" id="rl"></div>
						<div
							class="flex flex-col items-center justify-center w-full p-5 bg-gray-900 bg-opacity-25 border border-gray-700 rounded-lg md:w-auto lg:p-10 backdrop-blur-lg"
						>
							<div class="flex flex-col items-center justify-center w-full h-full md:w-9/12">
								<div class="text-right">
									<h1 class="text-4xl font-bold text-gray-300 w-max">{item.title}</h1>
									<span class="text-lg text-left text-gray-500">{item.start} //</span>
									<p class="text-lg text-gray-300">{item.slug.description}</p>
									<div class="mt-5">
										<a
											class="px-4 py-2 mt-5 text-white border rounded-md border-primary-500"
											href="/tournaments/{item.slug.slug}">En savoir +</a
										>
										{#if item.can_register}
											<RegisterButton tournament_id={1} />
										{:else}
											<a
												class="px-4 py-2 mt-5 text-white rounded-md bg-primary-500"
												href="/tournaments/{item.slug.slug}">Voir les résultats</a
											>
										{/if}
									</div>
								</div>
							</div>
						</div>
					</div>
				{/if}
			{/each}
		</div>
	</div>
	<Footer />
</div>

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
	#oblivion {
		width: 33px;
		height: 33px;
		transition: all 0.1s ease-out;
	}

	.animate-spin {
		animation: rotate 1s linear infinite;
	}
	@keyframes rotate {
		0% {
			transform: rotate(0deg);
		}
		100% {
			transform: rotate(360deg);
		}
	}
</style>
