<?php

use App\Core\Csrf;

$flash_errors = $_SESSION['flash_errors'] ?? [];
unset($_SESSION['flash_errors']);

$errorMessages = [
        'fields_required' => 'All fields are required,<br>
                              please try again.',
        'username_invalid' => 'Your provided username is not valid,<br>
                               please try again.',
        'username_taken' => 'Your provided username is already taken,<br>
                             please try again.',
        'email_invalid' => 'Your provided email is not valid,<br>
                            please try again.',
        'email_taken' => 'Your provided email is already taken,<br>
                          please try again.',
        'password_invalid' => 'Your password must be in the following format:<br>
                               at least one lowercase, one uppercase, one digit, no spaces<br>
                               length of 12-64,<br>
                               please try again.',
        'password_mismatch' => 'Your passwords did not match,<br>
                                please try again.',
        'registration_failed' => 'Registration failed due to a server error,<br>
                                  please try again later.',
];
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

        <!-- TODO: Give username/email/password requirements hints -->
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