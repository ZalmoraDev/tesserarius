<?php

use App\Core\Csrf;

$flash_errors = $_SESSION['flash_errors'] ?? [];
unset($_SESSION['flash_errors']);

$errorMessages = [

];
?>

<!-- TODO: Add error messages for project creation validation -->

<body class="tess-base-body flex flex-col">

<?php include_once __DIR__ . "/../skeleton/navbar.php"; ?>

<main class="flex-1 flex flex-col gap-10 w-full max-w-full justify-center items-center overflow-y-auto relative">
    <div class="tess-base-container-md">
        <div class="flex flex-col justify-center items-center gap-2">
            <h1 class="text-4xl">Create Project</h1>
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
            <form action="/project/create" method="POST" class="flex flex-col justify-center items-center gap-2 w-full">
                <input type="hidden" name="csrf" value="<?= Csrf::getToken() ?>">
                <input type="text" class="tess-input-md" placeholder="Project Name [3-32]" name="name" required>
                <textarea class="tess-input-md min-h-32" placeholder="Description [0-128]" name="description"
                          required></textarea>
                <button type="submit" class="tess-btn-pri w-full mt-4 cursor-pointer">Create Project</button>
            </form>
        </div>
        <a href="/" class="text-white underline">Back to home</a>
    </div>
</main>
</body>