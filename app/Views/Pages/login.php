<?php

use App\Core\Csrf;

?>

<body class="tess-base-body">
<main class="flex-1 flex flex-col gap-10 w-full max-w-full justify-center items-center overflow-y-auto">
    <section class="tess-base-container-md">
        <header class="tess-base-container-sm w-30 h-30">
            <img src="/assets/icons/logo/logoW.svg" alt="<?= $_ENV['SITE_NAME'] ?> logo">
        </header>
        <header class="flex flex-col justify-center items-center gap-2">
            <h1 class="text-4xl"><?= $_ENV['SITE_NAME'] ?></h1>
            <p class="text-neutral-400">Your tasks safeguarded</p>
        </header>
        <section class="gap-4 flex flex-col w-full items-center">
            <form action="/auth/login" method="POST" class="flex flex-col justify-center items-center gap-2">
                <input type="hidden" name="csrf" value="<?= Csrf::getToken() ?>">
                <input type="email" class="tess-input-md" placeholder="Email" name="email" required aria-label="Email address">
                <input type="password" class="tess-input-md" placeholder="Password" name="password" required aria-label="Password">
                <button type="submit" class="tess-btn-pri w-full mt-4 cursor-pointer">Login</button>
            </form>
            <p class="text-neutral-400">Don't have an account?
                <a href="/signup" class="text-white underline cursor-pointer">Sign up</a></p>
        </section>
    </section>
</main>
</body>