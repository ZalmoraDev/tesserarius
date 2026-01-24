<?php

use App\Core\Csrf;

// variables injected and path redirected by
// ProjectController::editProjectView
$vm = $params['vm'] ?? null;
$flash_errors = $_SESSION['flash_errors'] ?? [];
unset($_SESSION['flash_errors']);
?>

<body class="tess-base-body flex flex-col">

<?php
include_once __DIR__ . "/../skeleton/navbar.php";
if ($flash_errors)
    include __DIR__ . '/../components/toastComp.php';
?>

<!-- TODO: Compare logged-in user with $vm->project->ownerId to conditionally show/hide promote/demote/remove buttons -->

<main class="flex-1 flex flex-col gap-10 w-full max-w-full justify-center items-center overflow-y-auto relative">
    <div class="flex flex-col gap-6">
        <h1 class="tess-base-container-sm text-2xl w-full max-w-full mt-4">Edit project: <?= $vm->project->name ?></h1>

        <!-- 2x2 / 1x4 GRID -->
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">

            <!-- TOP LEFT | Project Invites -->
            <div class="tess-base-container-md gap-4 flex flex-col w-full items-center">
                <h2 class="text-2xl">Project Invites</h2>
                <form action="/project-members/create-invites/<?= $vm->project->id ?>" method="POST" class="w-full">
                    <input type="hidden" name="csrf" value="<?= Csrf::getToken() ?>">

                    <table class="w-full border-collapse">
                        <thead>
                        <tr class="border-b">
                            <th class="text-left p-2">Code</th>
                            <th class="text-left p-2">Creator</th>
                            <th class="text-left p-2">Created At</th>
                            <th class="text-left p-2">Expires At</th>
                            <th class="text-left p-2">Activated At</th>
                        </tr>
                        </thead>

                        <tbody>
                        <?php foreach ($vm->invites as $invite): ?>
                            <tr class="border-b">
                                <td class="p-2"><?= htmlspecialchars($invite->inviteCode) ?></td>
                                <td class="p-2"><?= htmlspecialchars($invite->createdBy) ?></td>
                                <td class="p-2"><?= htmlspecialchars($invite->createdAt->format('Y-m-d H:i')) ?></td>
                                <td class="p-2"><?= htmlspecialchars($invite->expiresAt->format('Y-m-d H:i')) ?></td>
                                <td class="p-2"><?= $invite->activatedAt ? htmlspecialchars($invite->activatedAt->format('Y-m-d H:i')) : '-' ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
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

            <!-- TOP RIGHT | Edit members -->
            <div class="tess-base-container-md gap-4 flex flex-col w-full items-center">
                <h2 class="text-2xl">Edit members</h2>

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
                    <?php foreach ($vm->members as $member): ?>
                        <tr class="border-b">
                            <td class="p-2"><?= htmlspecialchars($member->username) ?></td>
                            <td class="p-2"><?= htmlspecialchars($member->userRole->value) ?></td>

                            <td class="p-2">
                                <div class="flex gap-2">

                                    <!-- PROMOTE, Owner Only -->
                                    <?php if ($member->userRole->value === 'member'): ?>
                                        <form method="POST"
                                              action="/project/<?= $vm->project->id ?>/members/<?= $member->id ?>/promote">
                                            <input type="hidden" name="csrf" value="<?= Csrf::getToken() ?>">
                                            <button
                                                    class="w-9 h-9 flex items-center justify-center bg-blue-500 hover:bg-blue-600 text-white rounded">
                                                ↑
                                            </button>
                                        </form>
                                    <?php endif; ?>

                                    <!-- DEMOTE, Owner only -->
                                    <?php if ($member->userRole->value === 'admin'): ?>
                                        <form method="POST"
                                              action="/project/<?= $vm->project->id ?>/members/<?= $member->id ?>/demote">
                                            <input type="hidden" name="csrf" value="<?= Csrf::getToken() ?>">
                                            <button
                                                    class="w-9 h-9 flex items-center justify-center bg-yellow-500 hover:bg-yellow-600 text-white rounded">
                                                ↓
                                            </button>
                                        </form>
                                    <?php endif; ?>

                                    <!-- REMOVE, Owner CANNOT be removed. Admins CANNOT remove other admins. Admins CAN remove members -->
                                    <form method="POST"
                                          action="/project/<?= $vm->project->id ?>/members/<?= $member->id ?>/remove">
                                        <input type="hidden" name="csrf" value="<?= Csrf::getToken() ?>">
                                        <button
                                                class="w-9 h-9 flex items-center justify-center bg-red-500 hover:bg-red-600 text-white rounded">
                                            ✕
                                        </button>
                                    </form>

                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>


            <!-- BOTTOM LEFT | Edit Project -->
            <div class="tess-base-container-md gap-4 flex flex-col w-full items-center">
                <h2 class="text-2xl">Edit project</h2>
                <form action="/project/edit/<?= $vm->project->id ?>" method="POST"
                      class="flex flex-col justify-center items-center gap-2 w-full">
                    <input type="hidden" name="csrf" value="<?= Csrf::getToken() ?>">
                    <input type="text" class="tess-input-md w-full" placeholder="Project Name [3-32]" name="name"
                           value="<?= $vm->project->name ?>" required>
                    <textarea class="tess-input-md min-h-32 w-full" placeholder="Description [0-128]"
                              name="description"><?= $vm->project->description ?></textarea>
                    <button type="submit" class="tess-btn-sec w-full mt-4 cursor-pointer">Confirm edit</button>
                </form>
            </div>

            <!-- BOTTOM RIGHT | Delete Project-->
            <div class="tess-base-container-md gap-4 flex flex-col w-full items-center mb-4">
                <h2 class="text-2xl">Delete project</h2>
                <form action="/project/delete/<?= $vm->project->id ?>" method="POST" class="w-full">
                    <input type="hidden" name="csrf" value="<?= Csrf::getToken() ?>">
                    <p> Repeat project name to confirm deletion: </p>
                    <input type="text" class="tess-input-md w-full" placeholder="Project Name" name="confirm_name"
                           required>
                    <button type="submit"
                            class="cursor-pointer tess-btn-pri bg-red-600 hover:bg-red-700 text-white font-bold w-full mt-4">
                        CONFIRM DELETION
                    </button>
                </form>
            </div>

        </div>
    </div>
</main>
</body>

<!-- Lambda JS function to auto-set invite expiry date to 48 hours from now -->
<script>
    (() => {
        const input = document.getElementById('expires_at');
        const d = new Date();
        d.setDate(d.getDate() + 2);

        // format: YYYY-MM-DDTHH:MM
        input.value = d.toISOString().slice(0, 16);
    })();
</script>