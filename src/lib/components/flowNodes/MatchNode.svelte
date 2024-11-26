<script>
	import { Handle, Position } from '@xyflow/svelte';
	import MatchCard from '../others/MatchCard.svelte';
	export let data;
	export let isConnectable;

	const { name, logo, match, showTime } = data;
</script>

<div class="min-w-60">
	<Handle type="target" position={Position.Left} {isConnectable} />
	<MatchCard {name} {logo} {match} {showTime} />
	<Handle type="source" position={Position.Right} id="a" {isConnectable} />
</div>
