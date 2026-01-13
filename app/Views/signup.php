<?php

use App\Core\Csrf;

$flash_errors = $_SESSION['flash_errors'] ?? [];
unset($_SESSION['flash_errors']);

$errorMessages = [
        'invalid_credentials' => 'Invalid username or password,<br>please try again.',
        'requires_login' => 'You must log in using an account,<br>please try again.',
];
?>
<!-- TODO: Validate fields before submission -->
<body class="tess-base-body">
<main class="flex-1 flex flex-col gap-10 w-full max-w-full justify-center items-center overflow-y-auto">
    <div class="tess-base-container-md">
        <img src="/assets/icons/logo/logoW.svg" alt="<?= $_ENV['SITE_NAME'] ?> logo"
             class="tess-base-container-sm w-30 h-30">
        <div class="flex flex-col justify-center items-center gap-2">
            <h1 class="text-4xl">Sign up</h1>
            <p class="text-neutral-400 items-center">Your journey starts here</p>
        </div>

        <!-- START Error Messages, $flash_errors set in calling controller -->
        <?php if ($flash_errors): ?>
            <div class="text-red-600 text-center space-y-2">
                <?php foreach ($flash_errors as $e): ?>
                    <?php if (isset($errorMessages[$e])): ?>
                        <span><?= $errorMessages[$e] ?></span>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <!-- END Error Messages -->

        <div class="gap-4 flex flex-col w-full items-center">
            <form action="/auth/signup" method="POST" class="flex flex-col justify-center items-center gap-2">
                <input type="hidden" name="csrf" value="<?= Csrf::token() ?>">
                <input type="text" class="tess-input-md" placeholder="Username" name="username" required>
                <input type="text" class="tess-input-md mb-4" placeholder="Email" name="email" required>
                <input type="password" class="tess-input-md" placeholder="Password" name="password" required>
                <input type="password" class="tess-input-md" placeholder="Confirm Password" name="password_confirm" required>
                <button type="submit" class="tess-btn-pri w-full mt-4 cursor-pointer">Enter</button>
            </form>
        </div>
        <p class="text-neutral-400">Have an account?
            <a href="/login" class="text-white underline">Log in</a></p>
    </div>
</main>
</body>