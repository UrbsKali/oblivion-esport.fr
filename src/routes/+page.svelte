<script>
	import Topbar from '$lib/components/Topbar.svelte';
	import { onMount } from 'svelte';
	import * as THREE from 'three';
	import { GLTFLoader } from 'three/addons/loaders/GLTFLoader.js';
	import { currentOrigin } from '$lib/config';
	import Title from '$lib/components/Title.svelte';
	import Cursor from '$lib/components/Cursor.svelte';

	onMount(() => {
		const rl = document.querySelector('#rl');

		const scene = new THREE.Scene();
		const camera = new THREE.PerspectiveCamera(
			75,
			window.innerWidth / window.innerHeight,
			0.1,
			1000
		);
		camera.position.z = 5;

		const renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
		renderer.setSize(window.innerWidth - 20, window.innerHeight - 20);
		rl.appendChild(renderer.domElement);

		const gltfLoader = new GLTFLoader();
		let car = null;
		gltfLoader.load(currentOrigin() + '/3d/octane.glb', (gltf) => {
			scene.add(gltf.scene);
			// scale it
			gltf.scene.scale.set(0.03, 0.03, 0.03);
			// move the car (inside the scene) to center of rotation
			gltf.scene.children[0].position.x = 0;
			gltf.scene.children[0].position.y = 0;
			gltf.scene.children[0].position.z = 0;
			// rotate it
			gltf.scene.children[0].rotation.x = -Math.PI / 8;

			// rotate it
			/*
			gltf.scene.rotation.x = -Math.PI / 16;
			gltf.scene.rotation.z = Math.PI / 3;
			gltf.scene.rotation.y = Math.PI / 6;*/

			car = gltf.scene;
		});

		// add lights
		const light = new THREE.AmbientLight(0xffffff, 10);
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
	});
</script>

<Topbar />
<div class="border-b border-gray-700 landing">
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
			src="{currentOrigin()}/Teaser.mp4"
			class="absolute top-0 left-0 object-cover w-full h-full opacity-50 pointer-events-none -z-10"
			autoplay
			loop
			muted
		></video>
	</div>
</div>
<div class="border-b border-gray-700 rl">
	<div class="h-screen" id="rl"></div>
</div>
<div class="border-b border-gray-700 lol">
	<div class="h-screen"></div>
</div>

<Cursor />

<style>
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
