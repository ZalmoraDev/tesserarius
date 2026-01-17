<?php

use App\Views\components\compProjectHomeTab;

$projectTab = new compProjectHomeTab();

// Ignore error $projects (injected by View::render)
$projectsOwned = $projects['owned'];
$projectsMember = $projects['member'];

// TODO: Rework to new flash message system, maybe a toast notification?
// Get error message from URL (if present)
$error = $_GET['error'] ?? null;
?>

<body class="tess-base-body flex flex-col">

<?php include_once __DIR__ . "/../skeleton/navbar.php"; ?>

<main class="flex-1 flex flex-col gap-10 w-full max-w-full justify-center items-center overflow-y-auto">
    <div class=" gap-4 flex flex-col">
        <div class="tess-base-container-sm w-full">
            <h1>// Your projects [ <?= count($projectsOwned) ?> ]</h1>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">

            <?php // Generate 'Your Projects' tabs (User = UserRole::Owner)
            foreach ($projectsOwned as $projectAdmin) {
                echo $projectTab->printProjectsTabs($projectAdmin);
            }
            echo $projectTab->printAddProjectTab();
            ?>

        </div>
    </div>
    <hr>
    <div class=" gap-4 flex flex-col">
        <div class="tess-base-container-sm w-full">
            <h1>// Member Projects [ <?= count($projectsMember) ?> ]</h1>
        </div>
        <div id="projectGridContainer" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">

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