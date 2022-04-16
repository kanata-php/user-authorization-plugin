<?php
$this->layout('auth::layouts/admin', array_merge($this->data, [
    'is_logged' => $is_logged,
    'custom_left_sidebar' => 'auth::admin/api/parts/left-sidebar',
]));
?>

<div x-data="apiTokenForm()" class="grid grid-cols-1 gap-6">
    <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
        <div class="">
            <h3 class="text-lg font-medium leading-6 text-gray-900">Generate Token</h3>
            <p class="mt-1 text-sm text-gray-500">Generate new Api tokens.</p>
        </div>

        <hr class="my-4"/>

        <?php if (isset($errors['form'])) { ?>
            <div class="text-red-500 mb-4"><?=$errors['form']?></div>
        <?php } ?>

        <?php if (isset($success['form'])) { ?>
            <div class="text-green-500 mb-4"><?=$success['form']?></div>
        <?php } ?>

        <form method="POST" action="<?= route('api-tokens-generate') ?>" class="grid grid-cols-1 gap-6 mt-5 md:mt-0">
            <div class="space-y-6">
                <div class="grid grid-cols-3 gap-6">
                    <div class="col-span-3">
                        <label for="company-website" class="block text-sm font-medium text-gray-700">Name</label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                            <input x-model="data.name" type="text" name="name" id="name" class="focus:ring-blue-500 focus:border-blue-500 flex-1 block w-full rounded-none rounded-r-md sm:text-sm border-gray-300" placeholder="Token Name">
                        </div>
                        <?php if (isset($errors['name'])) { ?>
                            <div class="text-red-500 mb-4"><?=$errors['name']?></div>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="grid grid-cols-3 gap-6">
                    <div class="col-span-3">
                        <label for="company-website" class="block text-sm font-medium text-gray-700">Domain<span class="ml-2 text-xs text-gray-400">(leave it empty if no domain restriction is needed)</span></label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                            <input readonly class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm w-20 cursor-pointer" type="text" name="aud_protocol" id="aud_protocol" x-model="data.audProtocol" @click="toggleAudProtocol()">
                            <input x-model="data.aud" type="text" name="aud" id="aud" class="focus:ring-blue-500 focus:border-blue-500 flex-1 block w-full rounded-none rounded-r-md sm:text-sm border-gray-300" placeholder="tcpigeon.com">
                        </div>
                        <?php if (isset($errors['aud'])) { ?>
                            <div class="text-red-500 mb-4"><?=$errors['aud']?></div>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="grid grid-cols-3 gap-6">
                    <div class="col-span-3">
                        <label for="company-website" class="block text-sm font-medium text-gray-700">Expires at <span class="ml-2 text-xs text-gray-400">(leave it empty if no expire date is needed)</span></label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                            <input x-model="data.expire_at" type="datetime-local" name="expire_at" id="expire_at" class="focus:ring-blue-500 focus:border-blue-500 flex-1 block w-full rounded-none rounded-r-md sm:text-sm border-gray-300">
                        </div>
                        <?php if (isset($errors['expire_at'])) { ?>
                            <div class="text-red-500 mb-4"><?=$errors['expire_at']?></div>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="grid grid-cols-3 gap-6">
                    <div class="col-span-3">
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">Generate</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
        <div class="">
            <h3 class="text-lg font-medium leading-6 text-gray-900">Tokens</h3>
            <p class="mt-1 text-sm text-gray-500">Existent tokens.</p>
        </div>

        <hr class="my-4"/>

        <?php if (isset($errors['table-list'])) { ?>
            <div class="text-red-500 mb-4"><?=$errors['table-list']?></div>
        <?php } ?>

        <?php if (isset($success['table-list'])) { ?>
            <div class="text-green-500 mb-4"><?=$success['table-list']?></div>
        <?php } ?>

        <div class="grid grid-cols-1 gap-6 md:mt-0">
            <div class="flex flex-col">
                <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">
                        <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                            <table class="min-w-full divide-y divide-gray-300">
                                <thead class="bg-gray-50">
                                <tr class="grid grid-cols-3 md:grid-cols-4 lg:grid-cols-5">
                                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Name</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Token</th>

                                    <th scope="col" class="hidden md:block px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Expires at</th>

                                    <th scope="col" class="hidden lg:block px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Domain</th>

                                    <th scope="col" class="px-3 py-3.5 text-right text-sm font-semibold text-gray-900">Actions</th>
                                </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">

                                <template x-if="tokens.length === 0">
                                    <tr>
                                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6" colspan="4">No tokens yet.</td>
                                    </tr>
                                </template>

                                <template x-for="token in tokens">
                                    <tr class="grid grid-cols-3 md:grid-cols-4 lg:grid-cols-5">
                                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6" x-text="token.name"></td>

                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 flex">
                                            <span x-text="token.token.substr(0, 10) + (token.token.length > 10 ? '...' : '')"></span>
                                            <button :id="'token-copied-' + token.id" class="ml-4 has-tooltip" @click="copyToClipboard('token-copied-' + token.id, token.token)">
                                                <span class='tooltip hidden absolute rounded shadow-lg py-1 px-2 bg-black text-white -mt-10 -ml-12'></span>
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                                </svg>
                                            </button>
                                        </td>

                                        <td class="hidden md:block whitespace-nowrap px-3 py-4 text-sm text-gray-500" x-text="token.expire_at ?? '-'"></td>

                                        <td class="hidden lg:block whitespace-nowrap px-3 py-4 text-sm text-gray-500" x-text="token.aud ? token.aud_protocol + token.aud : '-'"></td>

                                        <td class="relative whitespace-nowrap py-4 pl-3 pr-4 flex justify-end text-sm font-medium sm:pr-6">
                                            <form method="POST" onsubmit="return confirm('Are you sure you want to delete this token?')" action="<?= route('api-tokens-delete') ?>">
                                                <input type="hidden" name="id" :value="token.id">
                                                <button type="submit" class="flex justify-end text-red-700 hover:text-red-900">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                    </svg>
                                                    <span class="ml-2 hidden md:block">
                                                        Delete<span class="sr-only" x-text="token.name"></span>
                                                    </span>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                </template>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function apiTokenForm() {
        return {
            data: {
                name: '',
                audProtocol: 'https://',
                aud: '',
                expire_at: '',
            },

            tokens: JSON.parse('<?= $tokens ?>'),

            tokensRows: [],

            toggleAudProtocol() {
                this.data.audProtocol = this.data.audProtocol === 'https://' ? 'http://' : 'https://';
            },

            copyToClipboard(id, content) {
                const textBlob = new Blob([content], { type: "text/plain" });
                const data = [new ClipboardItem({"text/plain": textBlob})];
                let tooltip = document.querySelector('#' + id + ' .tooltip');
                window.navigator.clipboard.write(data).then(function() {
                    tooltip.innerHTML = 'Copied!';
                    tooltip.classList.remove('hidden');
                    setTimeout(() => {tooltip.classList.add('hidden');}, 2000);
                }, function() {
                    tooltip.innerHTML = 'Failed to copy!';
                    tooltip.classList.remove('hidden');
                    setTimeout(() => {tooltip.classList.add('hidden');}, 2000);
                });
            },
        };
    }
</script>
