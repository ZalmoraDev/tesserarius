<?php

use App\Views\components\compProjectHomeTab;

$projectTab = new compProjectHomeTab();

// Ignore error $projects (injected by View::render)
$projectsOwned = $projects['owned'];
$projectsMember = $projects['member'];

$yourProjectsAmount = count($projectsOwned);
$memberProjectsAmount = count($projectsMember);

// TODO: Rework to new flash message system, maybe a toast notification?
// Get error message from URL (if present)
$error = $_GET['error'] ?? null;
?>

<body class="tess-base-body flex flex-col">

<?php include_once __DIR__ . "/../Views/skeleton/navbar.php"; ?>

<main class="flex-1 flex flex-col gap-10 w-full max-w-full justify-center items-center overflow-y-auto">
    <!-- Error Message -->
    <?php if ($error === "access_denied"): ?>
        <span class="text-red-600 text-center">You don't have access to this project.<br></span>
    <?php endif; ?>

    <div class=" gap-4 flex flex-col">
        <div class="tess-base-container-sm w-full">
            <h1>// Your projects [ <?= $yourProjectsAmount ?> ]</h1>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">

            <?php // Generate location cards based on HistoryLocationsGrid->generateLocationHTML()
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
            <h1>// Member Projects [ <?= $memberProjectsAmount ?> ]</h1>
        </div>
        <div id="projectGridContainer" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">

            <?php // Generate location cards based on HistoryLocationsGrid->generateLocationHTML()
            foreach ($projectsMember as $projectMember) {
                echo $projectTab->printProjectsTabs($projectMember);
            }
            echo $projectTab->printJoinProjectTab();
            ?>

        </div>
    </div>
</main>

</body>