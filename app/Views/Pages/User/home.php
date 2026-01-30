<?php

use App\Core\Csrf;
use App\Views\Components\projectHomeTabComp;

/** @var array $data /app/Core/View.php View::render*/

$projectTab = new projectHomeTabComp();

// variables injected and path redirected by
// UserController::homeView
$projectsOwned = $data['projects']['owned'] ?? null;
$projectsMember = $data['projects']['member'] ?? null;
?>

<body class="tess-base-body flex flex-col">
<?php include_once __DIR__ . "/../../Layouts/navbar.php"; ?>
<main class="flex-1 flex flex-col gap-10 w-full max-w-full justify-center items-center overflow-y-auto">
    <div class=" gap-4 flex flex-col mt-4">
        <div class="tess-base-container-sm w-full">
            <h1>// Your projects [ <?= count($projectsOwned) ?> ]</h1>
        </div>
        <div class="grid gap-4 grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 ">
            <?php // Generate 'Your Projects' tabs (User = UserRole::Owner)
            foreach ($projectsOwned as $projectAdmin) {
                echo $projectTab->printProjectsTabs($projectAdmin);
            }
            echo $projectTab->printAddProjectTab();
            ?>

        </div>
    </div>
    <div class=" gap-4 flex flex-col mb-4">
        <div class="tess-base-container-sm w-full">
            <h1>// Member Projects [ <?= count($projectsMember) ?> ]</h1>
        </div>
        <div class="grid gap-4 grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
            <!-- Generate 'Member Projects' tabs (User = UserRole::Admin | UserRole::Member) -->
            <?php foreach ($projectsMember as $projectMember)
                echo $projectTab->printProjectsTabs($projectMember); ?>
            <div class='tess-project-card flex flex-col items-center justify-center space-y-2'>
                <form action="/project-members/join-project" method="POST" class="flex flex-col gap-2">
                    <input type="hidden" name="csrf" value="<?= Csrf::getToken() ?>">
                    <input type='text' name="invite_code" placeholder='Enter Project Code' required minlength="16"
                           maxlength="16" class='tess-input-sm'>
                    <button class='tess-btn-pri cursor-pointer'>
                        Join
                    </button>
                </form>
            </div>
        </div>
    </div>
</main>

</body>