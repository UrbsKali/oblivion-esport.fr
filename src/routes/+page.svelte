<script>
	import Topbar from '$lib/components/Topbar.svelte';
</script>

<Topbar />
<div class="border-b border-gray-700 landing"></div>
<div class="border-b border-gray-700 rl">
	<div class="h-screen"></div>
</div>
<div class="border-b border-gray-700 lol">
	<div class="h-screen"></div>
</div>

<style>
	.landing {
		background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('/landing.png');
		background-size: cover;
		height: 100vh;
	}

	.landing::before {
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
	}
</style>
