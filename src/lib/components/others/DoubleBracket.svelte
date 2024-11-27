<script>
	import { SvelteFlowProvider } from '@xyflow/svelte';
	import DoubleBracketFlow from './DoubleBracketFlow.svelte';

	export let bracket = [];
</script>

<SvelteFlowProvider>
	<DoubleBracketFlow {bracket} />
</SvelteFlowProvider>
