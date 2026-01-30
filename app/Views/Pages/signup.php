<?php

use App\Core\Csrf;

include_once __DIR__ . "/../Layouts/navbar.php";
?>

<body class="tess-base-body">
<main class="flex-1 flex flex-col gap-10 w-full max-w-full justify-center items-center overflow-y-auto">
    <div class="tess-base-container-md">
        <img src="/assets/icons/logo/logoW.svg" alt="<?= $_ENV['SITE_NAME'] ?> logo"
             class="tess-base-container-sm w-30 h-30">
        <div class="flex flex-col justify-center items-center gap-2">
            <h1 class="text-4xl">Sign up</h1>
            <p class="text-neutral-400 items-center">Your journey starts here</p>
        </div>
        <!-- TODO: Give better username/email/password requirements hints -->
        <div class="gap-4 flex flex-col w-full items-center">
            <form action="/auth/signup" method="POST" class="flex flex-col justify-center items-center gap-2">
                <input type="hidden" name="csrf" value="<?= Csrf::getToken() ?>">
                <input type="text" class="tess-input-md" placeholder="Username" name="username" required>
                <input type="email" class="tess-input-md mb-4" placeholder="Email" name="email" required>
                <input type="password" class="tess-input-md" placeholder="Password" name="password" required
                       minlength="12">
                <input type="password" class="tess-input-md" placeholder="Confirm Password" name="password_confirm"
                       required minlength="12">
                <button type="submit" class="tess-btn-pri w-full mt-4 cursor-pointer">Enter</button>
            </form>
        </div>
        <p class="text-neutral-400">Have an account?
            <a href="/login" class="text-white underline">Log in</a></p>
    </div>
</main>
</body>