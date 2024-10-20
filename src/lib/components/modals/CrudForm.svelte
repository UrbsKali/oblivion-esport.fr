<script>
	// @ts-nocheck

	import { readonly } from 'svelte/store';

	export let type = 'Utilisateur';
	export let type_accord = 'un';
	export let action = 'Ajouter';
	export let fields = [];
	export let id = 'CrudModal';
	export let open = false;

	export let onSubmit = async () => {
		console.log('Submit');
	};

	$: for (let field of fields) {
		if (field.type === 'select' && field.value) {
			field.options = field.options.map((option) => {
				if (option.value === field.value) field.autoselect = true;
				return {
					...option,
					selected: option.value === field.value
				};
			});
		}
	}
</script>

<div
	{id}
	tabindex="-1"
	aria-hidden="true"
	class="{open
		? ''
		: 'hidden'} overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-full backdrop-blur-sm"
>
	<div class="relative flex w-full h-full p-4 m-auto">
		<!-- Modal content -->
		<div
			class="relative p-4 m-auto bg-white rounded-lg shadow dark:bg-gray-800 sm:p-5 min-w-96"
			id="CrudPopup"
		>
			<!-- Modal header -->
			<div class="flex justify-between mb-4 rounded-t sm:mb-5">
				<h3 class="text-lg font-semibold text-gray-900 dark:text-white">
					{action}
					{type_accord}
					{type}
				</h3>
				<button
					type="button"
					class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white"
					on:click={() => (open = false)}
				>
					<svg
						aria-hidden="true"
						class="w-5 h-5"
						fill="currentColor"
						viewBox="0 0 20 20"
						xmlns="http://www.w3.org/2000/svg"
						><path
							fill-rule="evenodd"
							d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
							clip-rule="evenodd"
						></path></svg
					>
					<span class="sr-only">Close modal</span>
				</button>
			</div>
			<!-- Modal body -->
			<form action="#">
				<div class="grid gap-4 mb-4 sm:grid-cols-2">
					{#each fields as field}
						<div class={field.wide ? 'col-span-2' : ''}>
							{#if field.type !== 'duplicate'}
								<label
									for="random"
									class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
									data-utils={field.data || ''}>{field.name}</label
								>
							{/if}
							{#if field.type === 'select'}
								<select
									id={field.id || field.name.toLowerCase()}
									name={field.id || field.name.toLowerCase()}
									class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
									on:change={field.onChange || null}
									readonly={field.readonly || false}
								>
									{#if (field.readonly || false) == false}<option
											selected={!field.autoselect}
											value="NULL">----------</option
										>
									{/if}
									{#each field.options as option}
										<option
											value={option.value}
											data-utils={option.data || ''}
											selected={option.selected}>{option.text}</option
										>
									{/each}
								</select>
							{:else if field.type === 'number'}
								<input
									type="number"
									name={field.id || field.name.toLowerCase()}
									id={field.id || field.name.toLowerCase()}
									class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
									placeholder={field.placeholder || field.name.toLowerCase()}
									required={field.required}
									value={field.value || ''}
									min={field.min || 0}
									max={field.max || 2000}
									step={field.step || 1}
									readonly={field.readonly || false}
								/>
							{:else if field.type === 'textarea'}
								<textarea
									name={field.id || field.name.toLowerCase()}
									id={field.id || field.name.toLowerCase()}
									class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
									placeholder={field.placeholder || field.name.toLowerCase()}
									required={field.required}
									value={field.value || ''}
									readonly={field.readonly || false}
								></textarea>
							{:else if field.type === 'img'}
								<input
									type="file"
									name={field.id || field.name.toLowerCase()}
									id={field.id || field.name.toLowerCase()}
									accept="image/png, image/jpeg"
									class="hidden"
									on:change={field.onChange ||
										((e) => {
											console.log(e.target.files[0]);
											const file = e.target.files[0];
											const reader = new FileReader();
											reader.onload = (e) => (field.value = e.target.result);
											reader.readAsDataURL(file);

											const label = document.querySelector(
												`label[for=${field.id || field.name.toLowerCase()}]`
											);
											label.innerHTML = '';
											const img = document.createElement('img');
											img.src = URL.createObjectURL(file);
											img.alt = field.name;
											img.className = 'object-contain w-full h-full rounded-lg ';
											label.appendChild(img);
										})}
								/>
								<label
									for={field.id || field.name.toLowerCase()}
									class="flex items-center justify-center w-full h-12 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
								>
									{#if field.value}
										<img
											src={field.value}
											alt={field.name}
											class="object-contain w-full h-full rounded-lg"
										/>
									{:else}
										<svg
											xmlns="http://www.w3.org/2000/svg"
											viewBox="0 0 24 24"
											class="w-6 h-6 fill-gray-400"
											><path
												d="M12 8.25a.75.75 0 0 1 .75.75v2.25H15a.75.75 0 0 1 0 1.5h-2.25V15a.75.75 0 0 1-1.5 0v-2.25H9a.75.75 0 0 1 0-1.5h2.25V9a.75.75 0 0 1 .75-.75Z"
											></path><path
												d="M3 3a2 2 0 0 1 2-2h9.982a2 2 0 0 1 1.414.586l4.018 4.018A2 2 0 0 1 21 7.018V21a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V3Zm2-.5a.5.5 0 0 0-.5.5v18a.5.5 0 0 0 .5.5h14a.5.5 0 0 0 .5-.5V7.018a.5.5 0 0 0-.146-.354l-4.018-4.018a.5.5 0 0 0-.354-.146H5Z"
											></path></svg
										>
									{/if}
								</label>
							{:else if field.type === 'duplicate'}
								<!--Duplicate is a + btn to replicate the last collumn -->
								<button
									type="button"
									class="flex items-center justify-center w-full h-8 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
									on:click={() => {
										const clean_filter = fields.filter((el) => el.type != 'duplicate');
										let lasts = []; // get the last full row, 1 if wide, 2 if not
										for (let i = clean_filter.length - 1; i >= 0; i--) {
											if (lasts.length == 2) break;
											if (clean_filter[i].wide) {
												lasts.push({ ...clean_filter[i] });
												break;
											} else {
												lasts.push({ ...clean_filter[i] });
											}
										}

										// drop the values of lasts to avoid duplicate
										lasts = lasts.map((el) => {
											el.value = '';
											el.data = '';
											// add number at the end of the id
											const num = parseInt(el.id.match(/\d+/g));
											if (num) {
												el.id = el.id.replace(/\d+/g, num + 1);
											} else {
												el.id = el.id + '_1';
											}
											return el;
										});

										fields = [
											...clean_filter,
											...lasts.reverse(),
											{ type: 'duplicate', wide: true }
										];
									}}
									>+
								</button>
							{:else if field.type === 'autocomplete'}
								<input
									type="text"
									id={field.id || field.name.toLowerCase()}
									class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
									placeholder={field.placeholder || field.name.toLowerCase()}
									required={field.required}
									value={field.value || ''}
									data-utils=""
									readonly={field.readonly || false}
									name={field.id || field.name.toLowerCase()}
									on:input={async (e) => {
										field.completion = await field.onChange(e);
									}}
								/>
								{#if field.completion?.length > 0}
									<div
										class="absolute z-10 block w-full p-2 pl-10 mt-1 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
									>
										{#each field.completion as c}
											<button
												on:click={(e) => {
													field.value = c.text;
													field.data = c.value;
													field.completion = [];
												}}
											>
												{c.text}
											</button>
											<br />
										{/each}
									</div>
								{/if}
							{:else}
								<input
									type={field.type}
									name={field.id || field.name.toLowerCase()}
									id={field.id || field.name.toLowerCase()}
									class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
									placeholder={field.placeholder || field.name.toLowerCase()}
									required={field.required}
									value={field.value || ''}
									readonly={field.readonly || false}
								/>
							{/if}
						</div>
					{/each}
				</div>
				<button
					type="submit"
					class="text-white inline-flex items-center bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800"
					on:click={onSubmit}
				>
					<svg
						class="w-6 h-6 mr-1 -ml-1"
						fill="currentColor"
						viewBox="0 0 20 20"
						xmlns="http://www.w3.org/2000/svg"
						><path
							fill-rule="evenodd"
							d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
							clip-rule="evenodd"
						></path></svg
					>
					{action}
					{type_accord}
					{type}
				</button>
			</form>
		</div>
	</div>
</div>
