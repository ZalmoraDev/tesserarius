<?php

use App\Views\components\projectHomeTabComp;

$projectTab = new projectHomeTabComp();

// injected by View::render
$projectsOwned = $params['projects']['owned'] ?? null;
$projectsMember = $params['projects']['member'] ?? null;

$flash_errors = $_SESSION['flash_errors'] ?? [];
unset($_SESSION['flash_errors']);

$errorMessages = [
        'already_logged_in' => 'You are already logged in,<br>
                                redirecting to home page.',
        'project_access_denied' => 'Access denied,<br>
                                    you are not a member of this project.',
        'project_insufficient_permissions' => 'Insufficient permissions,<br>
                                               please try again.',
];
?>

<body class="tess-base-body flex flex-col">

<?php
include_once __DIR__ . "/../skeleton/navbar.php";
if ($flash_errors)
    include __DIR__ . '/../components/toastComp.php';
?>

<main class="flex-1 flex flex-col gap-10 w-full max-w-full justify-center items-center overflow-y-auto">
    <div class=" gap-4 flex flex-col mt-4 ">
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



        <?php // Generate 'Member Projects' tabs (User = UserRole::Admin | UserRole::Member)
            foreach ($projectsMember as $projectMember) {
                echo $projectTab->printProjectsTabs($projectMember);
            }
            echo $projectTab->printJoinProjectTab();
            ?>

        </div>
    </div>
</main>

</body>