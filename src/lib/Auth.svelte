<script lang="ts">
	import { supabase } from './supabaseClient';

	let loading = false;
	let email = '';
	let password = '';

	const handleLogin = async () => {
		try {
			loading = true;
			const { data, error } = await supabase.auth.signInWithPassword({
				email: email,
				password: password
			});
			if (error) throw error;
		} catch (error) {
			if (error instanceof Error) {
				alert(error.message);
			}
		} finally {
			loading = false;
		}
	};
</script>

<div class="row flex-center flex">
	<div class="col-6 form-widget" aria-live="polite">
		<p class="description">Sign in</p>
		<form class="form-widget" on:submit|preventDefault={handleLogin}>
			<div>
				<label for="email">Email</label>
				<input
					id="email"
					class="inputField"
					type="email"
					placeholder="Your email"
					bind:value={email}
				/>
			</div>
			<div>
				<label for="password">Password</label>
				<input
					id="password"
					class="inputField"
					type="password"
					placeholder="********"
					bind:value={password}
				/>
			</div>
			<div>
				<button type="submit" class="button block" aria-live="polite" disabled={loading}>
					<span>{loading ? 'Loading' : 'Login'}</span>
				</button>
			</div>
		</form>
	</div>
</div>
