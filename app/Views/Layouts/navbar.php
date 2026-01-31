<?php

use App\Core\Csrf;
use App\Models\Enums\UserRole;

/** @var array $data /app/Core/View.php View::render */
?>

<nav class="w-full bg-neutral-900 gap-2 p-2 h-14 min-h-14
        shadow-[0_5px_10px_rgba(0,0,0,1)] flex justify-between items-center">
    <!-- Left -->
    <div class="flex flex-1 gap-4 justify-start items-center">
        <a href="/" class="flex items-center hover:brightness-50 mr-2">
            <img src="/assets/icons/logo/logoW.svg"
                 alt="" height="32" width="32"
                 class="w-8 h-8"/>
            <h1 class="text-xl"><?= $_ENV['SITE_NAME'] ?></h1>
        </a>
        <?php if ($data['user']['role'] !== UserRole::Member && !empty($data['project']->id ?? null)): ?>
            <a href="/project/view/<?= (int)$data['project']->id ?>"
               class="text-xl hover:brightness-50">
                View
            </a>
            <a href="/project/edit/<?= (int)$data['project']->id ?>"
               class="text-xl hover:brightness-50">
                Edit
            </a>
        <?php endif; ?>
    </div>

    <!-- Right -->
    <a href="/settings" class="flex gap-2 justify-end items-center hover:brightness-50 cursor-pointer">
        <div class="flex flex-col items-end">
            <span><?= escape($data['user']['username']) ?? "NO_USER" ?></span>
            <?php if (!empty($data['user']['role'])): ?>
                <span class="text-sm text-gray-400"><?= $data['user']['role']->value ?></span>
            <?php endif; ?>
        </div>
        <img src="/assets/icons/account_FFF.svg"
             alt="settings"
             height="32" width="32"
             class="w-8 h-8">
    </a>
</nav>