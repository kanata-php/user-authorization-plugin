<?php if (!isset($is_logged)) { ?>
    <!-- BEGIN: frontend -->
    <div class="flex items-center md:ml-12">
        <a href="<?= route('admin') ?>" class="text-base font-medium text-gray-500 hover:text-gray-900">
            Admin
        </a>
    </div>
    <!-- END: frontend -->
<?php } elseif ($is_logged) { ?>
    <!-- BEGIN: auth -->
    <div class="flex items-center md:ml-12">
        <button type="button" class="bg-gray-800 p-1 rounded-full text-gray-400 hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-white">
            <span class="sr-only">View notifications</span>
            <!-- Heroicon name: outline/bell -->
            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
        </button>

        <!-- Profile dropdown -->
        <div class="ml-3 relative">
            <div>
                <button @click="showUserMenu = !showUserMenu" type="button" class="bg-gray-800 flex text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-white" id="user-menu-button" aria-expanded="false" aria-haspopup="true">
                    <span class="sr-only">Open user menu</span>
                    <img class="h-8 w-8 rounded-full" src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt="">
                </button>
            </div>

            <div
                class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black z-40 ring-opacity-5 focus:outline-none"
                role="menu"
                aria-orientation="vertical"
                aria-labelledby="user-menu-button"
                tabindex="-1"
                x-show="showUserMenu"
                @click.away="showUserMenu = false"
                x-cloak
            >
                <!-- Active: "bg-gray-100", Not Active: "" -->
                <a href="<?= route('admin', []) ?>" class="hover:bg-gray-200 block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1" id="user-menu-item-0">Dashboard</a>
                <hr/>
                <a href="<?= route('api-tokens', []) ?>" class="hover:bg-gray-200 block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1" id="user-menu-item-0">Api Keys</a>
                <hr/>

                <!-- <a href="#" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1" id="user-menu-item-0">Your Profile</a> -->
                <!-- <a href="#" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1" id="user-menu-item-1">Settings</a> -->
                <a href="<?= route('logout-handler') ?>" class="hover:bg-gray-200 block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1" id="user-menu-item-2">Sign out</a>
            </div>
        </div>
    </div>
    <!-- END: auth -->
<?php } else { ?>
    <!-- BEGIN: no-auth -->
    <div class="flex items-center md:ml-12">
        <a href="<?= route('login') ?>" class="text-base font-medium text-gray-500 hover:text-gray-900">
            Sign in
        </a>
        <a href="<?= route('register') ?>" class="ml-8 inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-blue-600 hover:bg-blue-700">
            Sign up
        </a>
    </div>
    <!-- END: no-auth -->
<?php } ?>