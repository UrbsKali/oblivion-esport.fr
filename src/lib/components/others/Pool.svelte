<script>
	export let pool = {
		name: 'Pool B',
		teams: [
			{
				name: 'BZZ',
				match: 4,
				win: 4,
				logo: 'https://idlcqblimgotmibuednf.supabase.co/storage/v1/object/public/avatars/29342981-2bc0-4980-9b12-71fd5487c0b3/teams_jwmdgnihx6o1rchvqpzcv5.png'
			},
			{
				name: 'WDE',
				match: 5,
				win: 3,
				logo: 'https://idlcqblimgotmibuednf.supabase.co/storage/v1/object/public/avatars/1850792e-7425-462d-9c14-0cc8e73cf73a/teams_w4irqztd5is726f7n6tun9.png'
			},
			{
				name: 'NMS',
				match: 4,
				win: 2,
				logo: 'https://idlcqblimgotmibuednf.supabase.co/storage/v1/object/public/avatars/97e6631d-cef9-4614-9b3c-27f6674a59b5/teams_3bsy4jg8cn2wvvi1549gu.png'
			},
			{
				name: 'ICA',
				match: 5,
				win: 1,
				logo: 'https://idlcqblimgotmibuednf.supabase.co/storage/v1/object/public/avatars/edff3703-0ac5-4af5-bfa4-216750cc2b2c/teams_671gl7tdzuhsoqytfztbp.jpg'
			}
		]
	};

	let teams = pool.teams;

	let sortedTeams = teams.sort((a, b) => b.win - a.win);
</script>

<!-- Tailwind CSS table with dark mode and glassmorphism effect -->
<div class="rounded-lg overflow-clip w-fit">
	<table
		class="bg-gray-800 bg-opacity-0 divide-y divide-gray-700 rounded-lg shadow-lg backdrop-filter backdrop-blur-lg"
	>
		<thead>
			<tr>
				<th
					class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-300 uppercase bg-gray-900 bg-opacity-50"
				>
					Teams
				</th>
				<th
					class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-300 uppercase bg-gray-900 bg-opacity-50"
				>
					Match
				</th>
				<th
					class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-300 uppercase bg-gray-900 bg-opacity-50"
				>
					Win
				</th>
			</tr>
		</thead>
		<tbody class="bg-gray-800 bg-opacity-50 divide-y divide-gray-700">
			{#each sortedTeams as team}
				<tr>
					<td class="px-6 py-4 whitespace-no-wrap">
						<div class="flex items-center">
							<div class="flex-shrink-0 w-10 h-10">
								<img class="w-10 h-10 rounded-full" src={team.logo} alt="" />
							</div>
							<div class="ml-4">
								<div class="text-sm font-medium leading-5 text-gray-300">{team.name}</div>
							</div>
						</div>
					</td>
					<td class="px-6 py-4 whitespace-no-wrap">
						<div class="text-sm leading-5 text-gray-300">{team.match}</div>
					</td>
					<td class="px-6 py-4 whitespace-no-wrap">
						<div class="text-sm leading-5 text-gray-300">{team.win}</div>
					</td>
				</tr>
			{/each}
		</tbody>
	</table>
</div>
