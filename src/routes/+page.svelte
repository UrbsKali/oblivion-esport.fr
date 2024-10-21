<script>
	import { onMount } from 'svelte';

	import * as THREE from 'three';
	import { GLTFLoader } from 'three/addons/loaders/GLTFLoader.js';

	import Title from '$lib/components/utils/Title.svelte';
	import Cursor from '$lib/components/share/Cursor.svelte';
	import Footer from '$lib/components/share/Footer.svelte';

	let currentIndex = 0;
	const images = [
		{ src: 'https://placecats.com/350/200', alt: 'Image 1' },
		{ src: 'https://placecats.com/350/200', alt: 'Image 2' },
		{ src: 'https://placecats.com/350/200', alt: 'Image 3' }
	];

	const articles = [
		{ title: 'Article 1', description: 'Description for article 1' },
		{ title: 'Article 2', description: 'Description for article 2' },
		{ title: 'Article 3', description: 'Description for article 3' }
	];

	onMount(() => {
		const rl = document.querySelector('#rl');
		const twc = document.querySelector('#twc');

		initRL3D(rl);
		initTWC3D(twc);
	});

	function initTWC3D(renderElement) {
		const { width, height } = renderElement.getBoundingClientRect();

		const scene = new THREE.Scene();
		const camera = new THREE.PerspectiveCamera(75, width / height, 0.1, 1000);
		camera.position.z = 5;

		const renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
		renderer.setSize(width, height);
		renderElement.appendChild(renderer.domElement);

		const gltfLoader = new GLTFLoader();
		let car = null;
		gltfLoader.load('/v2/3d/TWC.glb', (gltf) => {
			scene.add(gltf.scene);
			// scale it
			gltf.scene.scale.set(1, 1, 1);

			// rotate it
			gltf.scene.children[0].rotation.x = -Math.PI / 8;
			gltf.scene.children[0].rotation.x = -Math.PI / 8;

			// move down
			gltf.scene.position.y = -0.25;

			// cast shadow
			gltf.scene.children[0].castShadow = true;

			car = gltf.scene;
		});

		// add lights
		const light = new THREE.AmbientLight(0xffffff, 1);
		light.position.set(0, 0, 10);
		scene.add(light);
		const light2 = new THREE.DirectionalLight(0xffffff, 5);
		light2.position.set(0, 10, 1);
		scene.add(light2);
		const light3 = new THREE.DirectionalLight(0xffffff, 5);
		light3.position.set(0, -10, 1);
		scene.add(light3);
		const light4 = new THREE.DirectionalLight(0xffffff, 5);
		light4.position.set(10, 0, 1);

		const animate = () => {
			if (car) {
				car.rotation.y += 0.001;
			}
			renderer.render(scene, camera);
			requestAnimationFrame(animate);
		};
		requestAnimationFrame(animate);
	}

	function initRL3D(renderElement) {
		const { width, height } = renderElement.getBoundingClientRect();

		const scene = new THREE.Scene();
		const camera = new THREE.PerspectiveCamera(75, width / height, 0.1, 1000);
		camera.position.z = 5;

		const renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
		renderer.setSize(width, height);
		renderElement.appendChild(renderer.domElement);

		const gltfLoader = new GLTFLoader();
		let car = null;
		gltfLoader.load('/v2/3d/EWC.glb', (gltf) => {
			scene.add(gltf.scene);
			// scale it
			gltf.scene.scale.set(10, 10, 10);
			// move the car (inside the scene) to center of rotation
			gltf.scene.children[0].position.x = 0;
			gltf.scene.children[0].position.y = 0;
			gltf.scene.children[0].position.z = 0;
			// rotate it
			gltf.scene.children[0].rotation.x = -Math.PI / 8;

			// move down
			gltf.scene.position.y = -1;

			car = gltf.scene;
		});

		// add lights
		const light = new THREE.AmbientLight(0xffffff, 1);
		light.position.set(0, 0, 10);
		scene.add(light);
		const light2 = new THREE.DirectionalLight(0xffffff, 5);
		light2.position.set(0, 10, 1);
		scene.add(light2);
		const light3 = new THREE.DirectionalLight(0xffffff, 5);
		light3.position.set(0, -10, 1);
		scene.add(light3);
		const light4 = new THREE.DirectionalLight(0xffffff, 5);
		light4.position.set(10, 0, 1);

		const animate = () => {
			if (car) {
				car.rotation.y += 0.001;
			}
			renderer.render(scene, camera);
			requestAnimationFrame(animate);
		};
		requestAnimationFrame(animate);
	}
</script>

<div class="border-b border-gray-700 landing enable-cursor">
	<div class="relative z-10 h-screen">
		<div class="flex items-center justify-center w-6/12 h-full">
			<div class="text-center">
				<Title size={200} />
				<p class="text-lg text-gray-300">Vivez l'expérience e-sport</p>
			</div>
		</div>
	</div>
	<div id="teaser">
		<video
			src="/v2/Teaser.mp4"
			class="absolute top-0 left-0 object-cover w-full h-full opacity-50 pointer-events-none -z-10"
			autoplay
			muted
		></video>
	</div>
</div>
<div class="w-full border-b border-gray-700">
	<div class="flex flex-col items-center justify-center w-full p-5">
		<h1 class="text-4xl font-bold text-white">Nos Tournois</h1>
	</div>

	<!-- ELEON WOLRD CUP -->
	<div class="flex flex-col justify-center gap-5 sm:flex-row">
		<div class="w-6/12 aspect-square min-w-96" id="rl"></div>
		<div class="flex flex-col items-center justify-center">
			<div class="flex flex-col items-center justify-center w-9/12 h-full">
				<div class="text-center">
					<h1 class="text-4xl font-bold text-gray-300 w-max">ELEON WORLD CUP</h1>
					<p class="text-lg text-gray-300">300 € de cashprize, en êtes-vous digne ?</p>
					<div>
						<button class="px-4 py-2 mt-5 text-white border rounded-md border-primary-500"
							>En savoir +</button
						>
						<button class="px-4 py-2 mt-5 text-white rounded-md bg-primary-500">Inscription</button>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- TWC 5 -->
	<div class="flex flex-col justify-center gap-5 sm:flex-row">
		<div class="flex flex-col items-center justify-center">
			<div class="flex flex-col items-center justify-center w-9/12 h-full">
				<div class="text-center">
					<h1 class="text-4xl font-bold text-gray-300 w-max">TRANKIL WOLD CUP</h1>
					<p class="text-lg text-gray-300">500 € de cashprize, Nabot viens m'aider !</p>
					<div>
						<button class="px-4 py-2 mt-5 text-white border rounded-md border-primary-500"
							>En savoir +</button
						>
						<button class="px-4 py-2 mt-5 text-white rounded-md bg-primary-500">Inscription</button>
					</div>
				</div>
			</div>
		</div>
		<div class="w-6/12 aspect-square min-w-96" id="twc"></div>
	</div>
</div>
<div class="border-b border-gray-700 lol">
	<div class="flex flex-col items-center justify-center w-full p-5">
		<h1 class="text-4xl font-bold text-white">Nos Actus</h1>
	</div>
	<div class="h-screen">
		<div class="flex justify-center h-full">
			<!-- Carousel -->
			<div class="w-1/4 h-full overflow-hidden">
				<div class="flex flex-col gap-5 carousel">
					{#each images as image, index}
						<div class="carousel-item {index === currentIndex ? 'active' : ''}">
							<img src={image.src} alt={image.alt} class="object-cover rounded-lg h-52" />
						</div>
					{/each}
				</div>
			</div>

			<!-- Article Details -->
			<div class="p-5 pt-10 text-white mt-52">
				<h2 class="text-3xl font-bold">{articles[currentIndex].title}</h2>
				<p class="mt-4 text-lg">{articles[currentIndex].description}</p>
			</div>
		</div>
	</div>
</div>
<Cursor />
<Footer />

<style>
	.carousel {
		display: flex;
		transition: transform 0.5s ease-in-out;
	}
	.carousel::after {
		content: '';
		width: 100%;
		height: 41.5rem;
		top: -42.75rem;
		position: relative;
		background: linear-gradient(
			rgba(17, 24, 39, 1) 0%,
			rgba(0, 0, 0, 0) 50%,
			rgba(17, 24, 39, 1) 100%
		);
		z-index: 1;
	}
	.carousel-item {
		min-width: 100%;
		transition: opacity 0.5s ease-in-out;
		opacity: 1;
	}
	.carousel-item.active {
		opacity: 1;
	}
	.landing {
		filter: brightness(0.8);
		height: 100vh;
		overflow: hidden;
	}

	.landing::after {
		content: '';
		height: 200vh;
		width: 100%;
		position: absolute;
		transform: rotate(25deg);
		top: -50%;
		left: -50%;
		z-index: 5;
		background-image: url('https://assets.codepen.io/1468070/plus-pattern-center.png');
		background-size: 5%;
		background-repeat: repeat;
		opacity: 0.1;
		animation: move 500s ease-in infinite;
	}
	#teaser::after {
		content: '';
		position: absolute;
		top: 0;
		left: 0;
		width: 100vw;
		height: 100vh;
		background-image: linear-gradient(
			115deg,
			rgba(17, 24, 39, 0.9) 20%,
			rgba(17, 24, 39, 0.5) 55%,
			rgba(17, 24, 39, 0) 60%
		);
		z-index: 1;
		background-blend-mode: multiply;
	}
	@keyframes move {
		100% {
			background-position: 0% 100%;
		}
	}
	@keyframes flicker {
		0% {
			opacity: 0.3;
		}
		50% {
			opacity: 1;
		}
		100% {
			opacity: 1;
		}
	}
</style>
