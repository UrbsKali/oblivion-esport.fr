import adapter from '@sveltejs/adapter-static';
import { vitePreprocess } from '@sveltejs/vite-plugin-svelte';


let extra_entries = [];

// Add all the pages in the /user/teams/ directory
for (let i = 1; i <= 100; i++) {
	extra_entries.push(`/user/teams/${i}`);
	extra_entries.push(`/admin/teams/${i}`);
}



/** @type {import('@sveltejs/kit').Config} */
const config = {
	kit: {
		// adapter-auto only supports some environments, see https://kit.svelte.dev/docs/adapter-auto for a list.
		// If your environment is not supported, or you settled on a specific environment, switch out the adapter.
		// See https://kit.svelte.dev/docs/adapters for more information about adapters.
		adapter: adapter({
			pages: 'build',
			assets: 'build',
			fallback: undefined,
			precompress: false,
			strict: true
		}),
		prerender: {
			entries: ['*', ...extra_entries],
		},
	},

	preprocess: vitePreprocess(),

};

export default config;
