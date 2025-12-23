<?php
global $view, $title;

// Get error message from URL (if present)
$error = $_GET['error'] ?? null;
?>

<body class="tess-base-body bg-">
<main class="flex-1 flex flex-col gap-10 w-full max-w-full justify-center items-center overflow-y-auto">
    <div class="tess-base-container-md">
        <img src="<?= SITE_URL ?>/assets/img/logo/logoW.svg" alt="Tesserarius logo" class="tess-base-container-sm w-30 h-30">
        <div class="flex flex-col justify-center items-center gap-2">
            <h1 class="text-4xl">Sign in</h1>
            <p class="text-neutral-400">Welcome to Tesserarius</p>
        </div>

        <!-- Error Messages -->
        <?php if ($error === "invalid_credentials"): ?>
            <span class="text-red-600 text-center">Invalid username or password.<br>Please try again.</span>
        <?php endif; ?>
        <?php if ($error === "direct_url_access"): ?>
            <span class="text-red-600 text-center">You must log in using an account.<br>Please try again.</span>
        <?php endif; ?>

        <form class="flex flex-col justify-center items-center gap-2" action="/auth" method="POST">
            <input type="text" class="tess-input-md" placeholder="Username" name="username" required>
            <input type="password" class="tess-input-md" placeholder="Password" name="password" required>
            <button type="submit" class="tess-btn-pri w-full mt-4 cursor-pointer">Login</button>
        </form>
    </div>
</main>
</body>