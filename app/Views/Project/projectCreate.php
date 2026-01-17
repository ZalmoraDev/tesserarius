<?php

use App\Core\Csrf;

$flash_errors = $_SESSION['flash_errors'] ?? [];
unset($_SESSION['flash_errors']);

$errorMessages = [
        'name_invalid' => 'Project name must be between 3 and 32 characters.',
        'description_invalid' => 'Description must be between 0 and 128 characters.',
        'name_duplicate' => 'You already have a project with this name.',
        'creation_failed' => 'Project creation failed. Please try again later.',
];
?>

<body class="tess-base-body flex flex-col">

<?php
include_once __DIR__ . "/../skeleton/navbar.php";
if ($flash_errors)
    include __DIR__ . '/../components/toastComp.php';
?>

<main class="flex-1 flex flex-col gap-10 w-full max-w-full justify-center items-center overflow-y-auto relative">
    <div class="tess-base-container-md">
        <div class="flex flex-col justify-center items-center gap-2">
            <h1 class="text-4xl">Create Project</h1>
        </div>
        <div class="gap-4 flex flex-col w-full items-center">
            <form action="/project/create" method="POST" class="flex flex-col justify-center items-center gap-2 w-full">
                <input type="hidden" name="csrf" value="<?= Csrf::getToken() ?>">
                <input type="text" class="tess-input-md" placeholder="Project Name [3-32]" name="name" required>
                <textarea class="tess-input-md min-h-32" placeholder="Description [0-128]"
                          name="description"></textarea>
                <button type="submit" class="tess-btn-pri w-full mt-4 cursor-pointer">Create Project</button>
            </form>
        </div>
        <a href="/" class="text-white underline">Back to home</a>
    </div>
</main>
</body>