<script>
	import { getAccessToken, getName } from '$lib/auth/riot';
	import { supabase } from '$lib/supabaseClient';
	import { userdata } from '$lib/store';
	import { goto } from '$app/navigation';

	let user;
	let sending = false;

	userdata.subscribe(async (value) => {
		if (value) {
			user = value;
			// check if a trailing slash is present
			if (window.location.pathname.slice(-1) === '/') await SetupRiot();
		}
	});

	async function SetupRiot() {
		if (sending) return;
		sending = true;
		// get code from url params
		const urlParams = new URLSearchParams(window.location.search);
		const code = urlParams.get('code');

		if (!code) {
			return;
		}

		const token = await getAccessToken(code);
		if (token.error) {
			console.log(token);
			alert(token.message);
			goto('/');
			return;
		}
		console.log(token);
		if (token) {
			const display_name = await getName(token.access_token);
			console.log(display_name);

			const info = {
				user_id: user.id,
				provider: 'riot',
				info: token,
				display_name: display_name
			};

			// insert into other_providers
			const { data, error } = await supabase.from('other_providers').insert(info).single();

			if (error) {
				console.log(error);
				alert('Error adding provider');
				goto('/');
				return;
			}

			// redirect to profile
			user.providers.push(info);
			userdata.set(user);
			goto('/user');
		}
		sending = false;
	}
</script>
