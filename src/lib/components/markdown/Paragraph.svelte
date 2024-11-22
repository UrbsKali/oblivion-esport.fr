<script>
	export let pre = false;
</script>

{#if pre}
	<pre class="pb-1.5 font-mono text-sm text-white"><slot /></pre>
{:else}
	<p class="pb-1.5 text-base text-justify text-white"><slot /></p>
{/if}
