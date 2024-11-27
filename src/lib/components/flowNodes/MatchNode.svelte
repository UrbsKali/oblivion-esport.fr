<script>
	import { Handle, Position } from '@xyflow/svelte';
	import MatchCard from '../others/MatchCard.svelte';
	export let data;
	export let isConnectable;

	const { name, logo, match, showTime, nb } = data;
</script>

<div class="flex items-center gap-2 align-middle min-w-60">
	<p class="w-4 text-gray-400 backdrop-blur-lg">{nb || 1}</p>
	<div class="w-full">
		<Handle type="target" position={Position.Left} {isConnectable} />
		<MatchCard {name} {logo} {match} {showTime} />
		<Handle type="source" position={Position.Right} id="a" {isConnectable} />
	</div>
</div>
