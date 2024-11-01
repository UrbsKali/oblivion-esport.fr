<script>
	import AuthForm from '$lib/components/utils/AuthForm.svelte';
</script>

<section class="min-h-screen overflow-hidden min-w-screen">
	<div class="pt-16 cross md:pt-0">
		<AuthForm />
	</div>
</section>

<style>
	.cross::after {
		content: '';
		height: 100vh;
		width: 100%;
		position: absolute;
		top: 0%;
		left: 0%;
		z-index: 0;
		background-image: url('/plus-pattern-center.webp');
		background-size: 5%;
		background-repeat: repeat;
		opacity: 0.1;
		animation: move 100s ease-in infinite;
	}
	@keyframes move {
		100% {
			background-position: 0% 100%;
		}
	}
</style>
