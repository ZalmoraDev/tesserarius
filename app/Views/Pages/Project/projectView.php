<?php

use App\Views\Components\ProjectTaskComp;

?>

<body class="tess-base-body flex flex-col">
<?php include_once __DIR__ . "/../../Layouts/navbar.php"; ?>
<main class="flex-1 flex justify-start items-start px-4 py-6 overflow-x-auto">
    <div class="flex gap-4 items-start min-w-max">
        <?php ProjectTaskComp::renderColumns($allColumnTasksArray); ?>
    </div>
</main>
<?php ProjectTaskComp::renderAddTaskModal(); ?>

<script src="/assets/scripts/taskModal.js" nonce="<?= $data['csp_nonce'] ?? '' ?>"></script>
</body>