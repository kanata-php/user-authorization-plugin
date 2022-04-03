<?php $this->layout('core::layouts/admin', ['hide_left_sidebar' => true, 'hide_top_bar' => true]) ?>

<div class="min-h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <a href="/">
            <img class="mx-auto h-16 w-auto" width="500" src="imgs/kanata.png" alt="<?=APP_NAME?>">
        </a>
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
            Registration
        </h2>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">

            <?php if (isset($message)) { ?>
                <div class="text-gray-900 mb-4"><?=$message?></div>
            <?php } ?>

        </div>
    </div>
</div>
