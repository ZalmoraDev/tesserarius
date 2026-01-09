<?php

use App\Core\Csrf;

$csrfService = new Csrf();

global $view, $title;

// Get error message from URL (if present)
// TODO: Find more elegant way of handling error messages
$error = $_GET['error'] ?? null;
?>

<body class="tess-base-body">
<main class="flex-1 flex flex-col gap-10 w-full max-w-full justify-center items-center overflow-y-auto">
    <div class="tess-base-container-md">
        <img src="<?= $_ENV['SITE_URL'] ?>/assets/icons/logo/logoW.svg" alt="<?php $_ENV['SITE_NAME'] ?> logo"
             class="tess-base-container-sm w-30 h-30">
        <div class="flex flex-col justify-center items-center gap-2">
            <h1 class="text-4xl">Sign in</h1>
            <p class="text-neutral-400">Signup</p>
        </div>

        <!-- Error Messages -->
        <?php if ($error === "invalid_credentials"): ?>
            <span class="text-red-600 text-center">Invalid username or password.<br>Please try again.</span>
        <?php endif; ?>
        <?php if ($error === "direct_url_access"): ?>
            <span class="text-red-600 text-center">You must log in using an account.<br>Please try again.</span>
        <?php endif; ?>

        <div class="gap-4 flex flex-col w-full items-center">
            <form action="/auth/signup" method="POST" class="flex flex-col justify-center items-center gap-2">
                <input type="hidden" name="csrf" value="<?= $csrfService->token() ?>">
                <label>
                    <input type="text" class="tess-input-md" placeholder="Username" name="username" required>
                </label>
                <label>
                    <input type="password" class="tess-input-md" placeholder="Password" name="password" required>
                </label>
                <button type="submit" class="tess-btn-pri w-full mt-4 cursor-pointer">Enter</button>
            </form>
        </div>
    </div>
</main>
</body>