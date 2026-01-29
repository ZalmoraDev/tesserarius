<?php

use App\Core\Csrf;

/** @var array $data /app/Core/View.php View::render */
?>

<nav class="w-full bg-neutral-900 gap-2 p-2
        shadow-[0_5px_10px_rgba(0,0,0,1)] flex justify-between">
    <!-- Left -->
    <div class="flex flex-1 gap-4 justify-start items-center">
        <a href="/" class="transition-colors flex items-center hover:brightness-50">
            <img src="/assets/icons/logo/logoW.svg"
                 alt="" height="32" width="32"
                 class="w-8 h-8"/>
            <h1 class="text-xl"><?= $_ENV['SITE_NAME'] ?></h1>
        </a>
    </div>

    <!-- Center -->
    <div class="flex flex-1 gap-4 justify-center items-center">
    </div>

    <!-- Right -->
    <div class="flex flex-1 gap-4 justify-end items-center">
        <p><?= (!empty($data['user']['role']) ? $data['user']['role'] . ': ' : '') . $data['user']['username'] ?? "NO_USER"; ?></p>
        <form action="/auth/logout" method="POST">
            <input type="hidden" name="csrf" value="<?= Csrf::getToken() ?>">
            <button type="submit" class="transition-colors cursor-pointer">
                <img src="/assets/icons/logout-FFF.svg"
                     alt="logout"
                     height="32" width="32"
                     class="w-8 h-8 hover:brightness-50">
            </button>
        </form>
    </div>
</nav>