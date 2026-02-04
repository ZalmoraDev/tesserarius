<?php

use App\Core\Csrf;

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

        <div class="gap-4 flex flex-col w-full items-center">
            <form action="/auth/signup" method="POST" class="flex flex-col justify-center items-center gap-4 w-full">
                <input type="hidden" name="csrf" value="<?= Csrf::getToken() ?>">

                <div class="flex flex-col gap-2">
                    <input type="text" class="tess-input-md" placeholder="Username [3-32]" name="username" required>
                    <input type="email" class="tess-input-md" placeholder="Email" name="email" required>
                </div>
                <div class="flex flex-col gap-2">
                    <input type="password" class="tess-input-md" placeholder="Password" name="password" required
                           minlength="12"
                           title="Must have: 1 lowercase, 1 uppercase, 1 digit, no spaces, 12-64 characters"
                           pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])\S{12,64}$">

                    <input type="password" class="tess-input-md" placeholder="Confirm Password" name="password_confirm"
                           required minlength="12">
                    <p class="text-xs text-neutral-400 w-full text-left mt-1">
                        Password must have:<br>1 lowercase, 1 uppercase, 1 digit,<br> no spaces, 12-64 chars
                    </p>
                </div>
                <button type="submit" class="tess-btn-pri w-full mt-4 cursor-pointer">Enter</button>
            </form>
        </div>
        <p class="text-neutral-400">Have an account?
            <a href="/login" class="text-white underline">Log in</a></p>
    </div>
</main>
</body>