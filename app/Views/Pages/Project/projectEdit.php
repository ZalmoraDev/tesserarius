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
$userRole = $data['user']['role'] ?? null;
?>

<body class="tess-base-body flex flex-col">

<?php include_once __DIR__ . "/../../Layouts/navbar.php"; ?>

<main class="flex-1 flex flex-col gap-10 w-full max-w-full justify-center items-center overflow-y-auto relative mb-4">
    <section class="flex flex-col gap-6">
        <header class="tess-base-container-sm text-2xl w-full max-w-full mt-4">
            <h1>Edit project: <?= Escaper::html($project->name) ?></h1>
        </header>

        <!-- 2x2 / 1x4 GRID -->
        <article class="grid grid-cols-1 xl:grid-cols-2 gap-4">

            <!-- TOP LEFT | Project Invites -->
            <section class="tess-base-container-md gap-4 flex flex-col w-full items-center justify-between">
                <h2 class="text-2xl justify-center">Project Invites</h2>
                <div class="flex-1 flex flex-col justify-center w-full gap-4">
                    <table class="w-full border-collapse">
                        <thead>
                        <tr class="border-b">
                            <th class="text-left p-2">Code</th>
                            <th class="text-left p-2">Creator</th>
                            <th class="text-left p-2">Created At</th>
                            <th class="text-left p-2">Expires At</th>
                            <th class="text-left p-2">Activated At</th>
                            <th class="text-left p-2">Actions</th>
                        </tr>
                        </thead>

                        <tbody>
                        <?php foreach ($invites as $invite): ?>
                            <!-- activatedAt uses 2 ternary checks: if activatedAt, show date, else if expired show EXPIRED, else show '-' -->
                            <tr class="border-b">
                                <td class="p-2"><?= Escaper::html($invite->inviteCode) ?></td>
                                <td class="p-2"><?= Escaper::html($invite->createdBy) ?></td>
                                <td class="p-2"><?= Escaper::html($invite->createdAt->format('Y-m-d H:i')) ?></td>
                                <td class="p-2"><?= Escaper::html($invite->expiresAt->format('Y-m-d H:i')) ?></td>
                                <td class="p-2"><?= $invite->activatedAt ? Escaper::html($invite->activatedAt->format('Y-m-d H:i')) :
                                            (new DateTimeImmutable() > $invite->expiresAt ? '<span class="text-red-600 font-semibold">EXPIRED</span>' : '-') ?></td>
                                <td class="p-2">
                                    <form method="POST"
                                          action="/project-members/delete-invite/<?= (int)$project->id ?>/<?= (int)$invite->id ?>">
                                        <input type="hidden" name="csrf" value="<?= Csrf::getToken() ?>">
                                        <button type="submit"
                                                class="w-9 h-9 flex items-center justify-center bg-red-500 hover:bg-red-600 text-white rounded cursor-pointer">
                                            ✕
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                    <form action="/project-members/create-invites/<?= (int)$project->id ?>" method="POST"
                          class="w-full">
                        <input type="hidden" name="csrf" value="<?= Csrf::getToken() ?>">
                        <div class="flex gap-4 w-full mt-4">
                            <div class="flex flex-col w-full">
                                <label for="expires_at" class="text-sm mb-1">Expires at</label>
                                <input type="datetime-local" id="expires_at" name="expires_at" required
                                       class="tess-input-md w-full">
                            </div>
                            <div class="flex flex-col w-32">
                                <label for="count" class="text-sm mb-1">Count</label>
                                <input type="number" id="count" name="count" min="1" max="10" value="1" required
                                       class="tess-input-md w-full">
                            </div>
                        </div>
                        <button type="submit" class="tess-btn-pri w-full bg-green-500 hover:bg-green-600 mt-4">
                            Create invite token(s)
                        </button>
                    </form>
                </div>
            </section>

            <!-- TOP RIGHT | Edit members -->
            <section class="tess-base-container-md gap-4 flex flex-col w-full items-center justify-between">
                <h2 class="text-2xl justify-center">Edit members</h2>

                <div class="flex-1 flex flex-col justify-center w-full">
                    <table class="w-full border-collapse">
                        <thead>
                        <tr class="border-b">
                            <th class="text-left p-2">Username</th>
                            <th class="text-left p-2">Role</th>
                            <th class="text-left p-2">Actions</th>
                        </tr>
                        </thead>

                        <tbody>
                        <!-- LIST MEMBERS -->
                        <?php foreach ($members as $member): ?>
                            <tr class="border-b">
                                <td class="p-2"><?= Escaper::html($member->username) ?></td>
                                <td class="p-2"><?= Escaper::html($member->userRole->value) ?></td>

                                <td class="p-2">
                                    <div class="flex gap-2">

                                        <!-- PROMOTE, Owner Only -->
                                        <?php if ($userRole === UserRole::Owner && $member->userRole === UserRole::Member): ?>
                                            <form method="POST"
                                                  action="/project-members/promote/<?= (int)$project->id ?>/<?= (int)$member->userId ?>">
                                                <input type="hidden" name="csrf" value="<?= Csrf::getToken() ?>">
                                                <button type="submit"
                                                        class="w-9 h-9 flex items-center justify-center bg-blue-500 hover:bg-blue-600 text-white rounded cursor-pointer">
                                                    ↑
                                                </button>
                                            </form>
                                        <?php endif; ?>

                                        <!-- DEMOTE, Owner only -->
                                        <?php if ($userRole === UserRole::Owner && $member->userRole === UserRole::Admin): ?>
                                            <form method="POST"
                                                  action="/project-members/demote/<?= (int)$project->id ?>/<?= (int)$member->userId ?>">
                                                <input type="hidden" name="csrf" value="<?= Csrf::getToken() ?>">
                                                <button type="submit"
                                                        class="w-9 h-9 flex items-center justify-center bg-yellow-500 hover:bg-yellow-600 text-white rounded cursor-pointer">
                                                    ↓
                                                </button>
                                            </form>
                                        <?php endif; ?>

                                        <!-- REMOVE, Owner CANNOT be removed. Admins CANNOT remove other admins. Admins CAN remove members -->
                                        <?php if ($member->userRole !== UserRole::Owner && ($userRole === UserRole::Owner ||
                                                        ($userRole === UserRole::Admin && $member->userRole === UserRole::Member))): ?>
                                            <form method="POST"
                                                  action="/project-members/remove/<?= (int)$project->id ?>/<?= (int)$member->userId ?>">
                                                <input type="hidden" name="csrf" value="<?= Csrf::getToken() ?>">
                                                <button type="submit"
                                                        class="w-9 h-9 flex items-center justify-center bg-red-500 hover:bg-red-600 text-white rounded cursor-pointer">
                                                    ✕
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- BOTTOM LEFT | Edit Project -->
            <section class="tess-base-container-md gap-4 flex flex-col w-full items-center justify-between">
                <h2 class="text-2xl justify-center">Edit project</h2>
                <div class="flex-1 flex flex-col justify-center w-full">
                    <form action="/project/edit/<?= (int)$project->id ?>" method="POST"
                          class="flex flex-col justify-center items-center gap-2 w-full">
                        <input type="hidden" name="csrf" value="<?= Csrf::getToken() ?>">
                        <input type="hidden" name="projectName" value="<?= $project->name ?>">
                        <input type="text" class="tess-input-md w-full" placeholder="Project Name [3-32]" name="name"
                               value="<?= Escaper::html($project->name) ?>" required aria-label="Project name, 3 to 32 characters">
                        <textarea class="tess-input-md min-h-32 w-full" placeholder="Description [0-128]"
                                  name="description" aria-label="Project description, 0 to 128 characters"><?= Escaper::html($project->description) ?></textarea>
                        <button type="submit" class="tess-btn-sec w-full mt-4 cursor-pointer">Confirm edit</button>
                    </form>
                </div>
            </section>

            <!-- BOTTOM RIGHT | Delete Project (Owner-only) -->
            <?php if ($userRole === UserRole::Owner): ?>
                <section class="tess-base-container-md gap-4 flex flex-col w-full items-center justify-between">
                    <h2 class="text-2xl justify-center">Delete project</h2>
                    <div class="flex-1 flex flex-col justify-center w-full">
                        <form action="/project/delete/<?= (int)$project->id ?>" method="POST"
                        <form action="/project/delete/<?= (int)$project->id ?>" method="POST" class="w-full flex flex-col">
                            <input type="hidden" name="csrf" value="<?= Csrf::getToken() ?>">
                            <p class="mb-2"> Repeat project name to confirm deletion: </p>
                            <input type="text" class="tess-input-md w-full" placeholder="Project Name"
                                   name="confirm_name"
                                   required aria-label="Confirm project name for deletion">
                            <button type="submit"
                                    class="cursor-pointer tess-btn-pri bg-red-600 hover:bg-red-700 text-white font-bold w-full mt-4">
                                CONFIRM DELETION
                            </button>
                        </form>
                    </div>
                </section>
            <?php endif; ?>

        </article>
    </section>
</main>
</body>

<!-- Lambda JS function to auto-set invite expiry date to 48 hours from now -->
<script nonce="<?= $data['csp_nonce'] ?? '' ?>">
    (() => {
        const input = document.getElementById('expires_at');
        const d = new Date();
        d.setDate(d.getDate() + 2);

        // format: YYYY-MM-DD HH:MM
        input.value = d.toISOString().slice(0, 16);
    })();
</script>