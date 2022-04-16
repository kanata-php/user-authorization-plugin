<div class="hidden lg:block lg:col-span-3 xl:col-span-2">
    <nav aria-label="Sidebar" class="sticky top-4 divide-y divide-gray-300">

        <?php
        $active_classes = 'bg-gray-200 text-gray-900';
        $normal_classes = 'text-gray-600 hover:bg-gray-50';
        ?>

        <div class="pb-8 space-y-1">
            <a
                href="<?= route('api-tokens') ?>"
                class="<?= ($current_route === 'api-tokens') ? $active_classes : $normal_classes ?> group flex items-center px-3 py-2 text-sm font-medium rounded-md"
                aria-current="page"
            >
                <!-- Heroicon name: outline/home -->
                <svg class="text-gray-500 flex-shrink-0 -ml-1 mr-3 h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span class="truncate">
                    Api tokens
                </span>
            </a>
        </div>
    </nav>
</div>