<script>
	export let title;
	export let description;
	export let image;
	export let url = '#';
	export let is_big = false;
</script>

<div
	class="flex flex-row items-end gap-5 p-5 bg-center bg-no-repeat bg-cover border border-gray-700 rounded-lg min-h-36 backdrop-blur-lg sm:h-48"
	style="background: linear-gradient(rgba(0, 0, 0, 0.5) 0%, rgba(0, 0, 0, 1) 100%), url({image});"
>
	<div class="flex flex-col gap-1">
		<h2 class="text-2xl font-bold text-white">{title}</h2>
		<div class="flex flex-col gap-5">
			<a href={url} class="px-4 py-2 text-center text-white rounded-md bg-primary-500 max-w-32"
				>Lire l'article</a
			>
		</div>
	</div>
</div>

<style>
	.lol {
		background: linear-gradient(rgba(0, 0, 0, 0) 0%, rgba(0, 0, 0, 1) 100%),
			url('/img/league-of-legends.jpg');
	}
</style>
