<script>
	import SvelteMarkdown from 'svelte-markdown';
	import { renderers } from '$lib/renderers';

	const source = `
  # Ressources & Liens

Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.

* This is a list
* With two items
  1. And a sublist
  2. That is ordered
     * With another
     * Sublist inside

| And this is | A table |
|-------------|---------|
| With two    | columns |


![img](https://camo.githubusercontent.com/993bec1aca87b5a95290435b9d4727634e1508af7a807111ac43a690db9d054a/68747470733a2f2f692e696d67686970706f2e636f6d2f66696c65732f644d375049313732323737383231302e706e67)

`;
</script>

<SvelteMarkdown {source} {renderers} />
