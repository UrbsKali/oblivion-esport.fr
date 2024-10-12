<script>
	import Topbar from '$lib/components/Topbar.svelte';
	import { onMount } from 'svelte';
	import * as THREE from 'three';
	import { GLTFLoader } from 'three/addons/loaders/GLTFLoader.js';
	import { OrbitControls } from 'three/addons/controls/OrbitControls.js';

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
		renderer.setSize(1200, 720);
		rl.appendChild(renderer.domElement);

		const gltfLoader = new GLTFLoader();
		gltfLoader.load('/3d/octane.glb', (gltf) => {
			scene.add(gltf.scene);
			// scale it
			gltf.scene.scale.set(0.03, 0.03, 0.03);
		});

		// add lights
		const light = new THREE.AmbientLight(0xffffff, 10);
		light.position.set(0, 0, 10);
		scene.add(light);
		const light2 = new THREE.DirectionalLight(0xffffff);
		light2.position.set(0, 10, 1);
		scene.add(light2);
		const light3 = new THREE.DirectionalLight(0xffffff);
		light3.position.set(0, -10, 1);
		scene.add(light3);
		const light4 = new THREE.DirectionalLight(0xffffff);
		light4.position.set(10, 0, 1);

		// add orbit controls
		const controls = new OrbitControls(camera, renderer.domElement);
		controls.enableDamping = true;
		controls.target.set(0, 0, 0);
		controls.update();

		// add grid helper
		const gridHelper = new THREE.GridHelper(10, 10);
		scene.add(gridHelper);

		const animate = () => {
			controls.update();
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
				<h1 class="text-4xl font-bold text-white">Welcome to Oblivion</h1>
				<p class="text-lg text-gray-300">A new way to enjoy Esport</p>
				<div class="mt-6">
					<a
						href="/"
						class="px-6 py-3 font-semibold text-white rounded-md bg-primary-500 hover:bg-primary-600"
						>Login</a
					>
					<a
						href="/"
						class="px-6 py-3 font-semibold text-white bg-gray-800 rounded-md hover:bg-gray-700"
						>Register</a
					>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="border-b border-gray-700 rl">
	<div class="h-screen" id="rl"></div>
</div>
<div class="border-b border-gray-700 lol">
	<div class="h-screen"></div>
</div>

<style>
	.landing {
		background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('/landing.png');
		background-size: cover;
		height: 100vh;
		z-index: -2;
	}

	.landing::after {
		content: '';
		background: linear-gradient(
			115deg,
			rgba(17, 24, 39, 1) 20%,
			rgba(17, 24, 39, 0.9) 55%,
			rgba(17, 24, 39, 0) 60%
		);
		height: 100vh;
		width: 100%;
		position: absolute;
		top: 0;
		left: 0;
		z-index: 0;
	}
</style>
