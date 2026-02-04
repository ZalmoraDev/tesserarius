<?php

use App\Core\Csrf;
use App\Core\Escaper;
use App\Models\Enums\UserRole;

/** @var array $data /app/Core/View.php View::render */

// variables injected and path redirected by
// ProjectController::editProjectView
$project = $data['project'] ?? null; // Project
$members = $data['members'] ?? []; // Project
$invites = $data['invites'] ?? []; // ProjectInvite[]

// injected by Router::dispatch() via AuthServiceInterface::requireProjectAccess
$userId = (int)$data['user']['id'] ?? null;
$username = Escaper::html($data['user']['username']) ?? null;
$email = Escaper::html($data['user']['email']) ?? null;
?>

<body class="tess-base-body flex flex-col">

<?php include_once __DIR__ . "/../../Layouts/navbar.php"; ?>

<main class="flex-1 flex flex-col gap-10 w-full max-w-full justify-center items-center overflow-y-auto relative mb-4">
    <section class="flex flex-col gap-6">
        <header class="tess-base-container-sm text-2xl w-full max-w-full mt-4">
            <h1>Settings</h1>
        </header>

        <!-- 2x2 / 1x4 GRID -->
        <article class="grid grid-cols-1 xl:grid-cols-2 gap-4">

            <!-- Top L&R | Logout -->
            <section class="xl:col-span-2 tess-base-container-md gap-4 flex flex-col w-full items-center justify-between">
                <form action="/auth/logout" method="POST" class="flex-1 flex flex-col justify-center w-full">
                    <input type="hidden" name="csrf" value="<?= Csrf::getToken() ?>">
                    <button type="submit" class="tess-btn-pri w-full cursor-pointer">Logout</button>
                </form>
            </section>

            <!-- BOTTOM LEFT | Edit Account -->
            <section class="tess-base-container-md gap-4 flex flex-col w-full items-center justify-between">
                <h2 class="text-2xl justify-center">Edit account</h2>
                <form action="/user/edit" method="POST"
                      class="flex-1 flex flex-col justify-center items-center gap-2 w-full">
                    <input type="hidden" name="csrf" value="<?= Csrf::getToken() ?>">
                    <input type="text" class="tess-input-md w-full" placeholder="Username" name="username"
                           value="<?= $username ?>" required aria-label="Username">
                    <input type="text" class="tess-input-md w-full" placeholder="Email" name="email"
                           value="<?= $email ?>" required aria-label="Email address">
                    <button type="submit" class="tess-btn-sec w-full mt-4 cursor-pointer">Confirm edit</button>
                </form>
            </section>

            <!-- BOTTOM RIGHT | Delete Project -->
            <section class="tess-base-container-md gap-4 flex flex-col w-full items-center justify-between">
                <h2 class="text-2xl justify-center">Delete account</h2>
                <form action="/user/delete" method="POST" class="flex-1 flex flex-col justify-center w-full">
                    <input type="hidden" name="csrf" value="<?= Csrf::getToken() ?>">
                    <p class="mb-2"> Repeat account name to confirm deletion: </p>
                    <input type="text" class="tess-input-md w-full" placeholder="Account Name"
                           name="confirm_username"
                           required aria-label="Confirm account name for deletion">
                    <button type="submit"
                            class="cursor-pointer tess-btn-pri bg-red-600 hover:bg-red-700 text-white font-bold w-full mt-4">
                        CONFIRM DELETION
                    </button>
                </form>
            </section>

        </article>
    </section>
</main>
</body>