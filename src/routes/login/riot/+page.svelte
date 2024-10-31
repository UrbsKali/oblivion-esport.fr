<script>
	import { onMount } from 'svelte';
	import { getAccessToken, getName } from '$lib/auth/epic';
	import { supabase } from '$lib/supabaseClient';
	import { userdata } from '$lib/store';
	import { goto } from '$app/navigation';

	let user;

	userdata.subscribe(async (value) => {
		if (value) {
			user = value;
			await SetupEpic();
		}
	});

	async function SetupEpic() {
		// get code from url params
		const urlParams = new URLSearchParams(window.location.search);
		const code = urlParams.get('code');

		if (!code) {
			return;
		}

		const token = await getAccessToken(code);
		console.log(token);
		if (token) {
			const display_name = getName(token.access_token);
			console.log(display_name);

			// insert into other_providers
			const { data, error } = await supabase
				.from('other_providers')
				.insert([
					{
						user_id: user.id,
						provider: 'epic',
						info: token,
						display_name: display_name
					}
				])
				.single();

			if (error) {
				console.log(error);
			}

			// redirect to profile
			goto('/v2/user');
		}
	}
</script>
