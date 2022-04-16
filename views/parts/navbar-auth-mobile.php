<?php if (!isset($is_logged)) { ?>
    <!-- BEGIN: frontend -->
    <div class="mt-6">
        <a href="<?= route('admin', []) ?>" class="w-full flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-blue-600 hover:bg-blue-700">
            Admin
        </a>
    </div>
    <!-- END: frontend -->
<?php } elseif ($is_logged) { ?>
    <!-- BEGIN: auth -->
    <div class="mt-6">
        <a href="<?= route('admin', []) ?>" class="w-full flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-blue-600 hover:bg-blue-700">
            Dashboard
        </a>
        <a href="<?= route('api-tokens', []) ?>" class="w-full flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-blue-600 hover:bg-blue-700">
            Api Keys
        </a>
        <a href="<?= route('logout-handler') ?>" class="w-full flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-blue-600 hover:bg-blue-700">
            Sign out
        </a>
    </div>
    <!-- END: auth -->
<?php } else { ?>
    <!-- BEGIN: no-auth -->
    <div class="mt-6">
        <a href="/register" class="w-full flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-blue-600 hover:bg-blue-700">
            Sign up
        </a>
        <p class="mt-6 text-center text-base font-medium text-gray-500">
            Existing customer?
            <a href="/login" class="text-blue-600 hover:text-blue-500">
                Sign in
            </a>
        </p>
    </div>
    <!-- END: no-auth -->
<?php } ?>