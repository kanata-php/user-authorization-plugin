<?php $this->layout('auth::layouts/admin', ['hide_left_sidebar' => true, 'hide_top_bar' => true]) ?>

<div class="min-h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <a href="/">
            <img class="mx-auto h-16 w-auto" width="500" src="/imgs/kanata.png" alt="<?=APP_NAME?>">
        </a>
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
            Registration
        </h2>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">

            <?php if (isset($errors['form'])) { ?>
                <div class="text-red-500 mb-4"><?=$errors['form']?></div>
            <?php } ?>

            <form class="space-y-6" action="<?=route('register-handler')?>" method="POST">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">
                        Name
                    </label>
                    <div class="mt-1">
                        <input id="name" name="name" type="text" required class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" value="<?=$name ?? ''?>">
                        <?php if (isset($errors['name'])) { ?>
                            <div class="text-red-500 mb-4"><?=$errors['name']?></div>
                        <?php } ?>
                    </div>
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">
                        Email address
                    </label>
                    <div class="mt-1">
                        <input id="email" name="email" type="email" autocomplete="email" required class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" value="<?=$email ?? ''?>">
                        <?php if (isset($errors['email'])) { ?>
                            <div class="text-red-500 mb-4"><?=$errors['email']?></div>
                        <?php } ?>
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">
                        Password
                    </label>
                    <div class="mt-1">
                        <input id="password" name="password" type="password" autocomplete="current-password" required class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <?php if (isset($errors['password'])) { ?>
                            <div class="text-red-500 mb-4"><?=$errors['password']?></div>
                        <?php } ?>
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">
                        Password Confirmation
                    </label>
                    <div class="mt-1">
                        <input id="password_confirmation" name="password_confirmation" type="password" required class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <?php if (isset($errors['password_confirmation'])) { ?>
                            <div class="text-red-500 mb-4"><?=$errors['password_confirmation']?></div>
                        <?php } ?>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember-me" name="remember-me" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="remember-me" class="ml-2 block text-sm text-gray-900">
                            Remember me
                        </label>
                    </div>

                    <div class="text-sm">
                        <a href="#" class="font-medium text-blue-600 hover:text-blue-500">
                            Forgot your password?
                        </a>
                    </div>
                </div>

                <div>
                    <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Register
                    </button>
                    <a href="<?=route('login')?>" class="mt-4 underline cursor-pointer font-medium block text-center">or Sign In</a>
                </div>
            </form>

            <?php
            // TODO: not implemented
            // if (config('authorization.social-login')) {
            //     $this->insert('auth::parts/social-login');
            // }
            ?>
        </div>
    </div>
</div>
