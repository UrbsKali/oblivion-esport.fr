<script>
	import { onMount } from 'svelte';
	import { getAccessToken, getUserInfo } from '$lib/epic';
	import { supabase } from '$lib/supabaseClient';
	import { userdata } from '$lib/store';

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
		const token = await getAccessToken(code);
		console.log(token);
		if (token) {
			const user = getUserInfo(token);
			console.log(user);

			// insert into other_providers
			const { data, error } = await supabase
				.from('other_providers')
				.insert([
					{
						user_id: user.id,
						provider: 'epic',
						info: token
					}
				])
				.single();
		}
	}
</script>
