<?php

use App\Models\Enums\TaskStatus;
use App\Views\Components\ProjectTaskComp;

/** @var array $data /app/Core/View.php View::render */

// Create empty array for each task status (Backlog, Todo, Doing etc.)
$tasksByStatus = [];
foreach (TaskStatus::cases() as $status)
    $tasksByStatus[$status->value] = [];

// Fill sub-arrays (Status Arrays) with corresponding tasks
foreach ($data['tasks'] ?? [] as $task)
    $tasksByStatus[$task->status->value][] = $task;
?>

<body class="tess-base-body flex flex-col" data-current-user-id="<?= $data['user']['id'] ?? '' ?>">
<?php include_once __DIR__ . "/../../Layouts/navbar.php"; ?>
<main class="flex-1 flex justify-start items-start px-4 py-6 overflow-x-auto">
    <div class="flex gap-4 items-start min-w-max">
        <?php ProjectTaskComp::renderColumns($tasksByStatus, $data['members'] ?? [], $data['user']['id'] ?? null); ?>
    </div>
</main>
<?php ProjectTaskComp::renderAddTaskModal($data['user']['username'], $data['members'], $data['project']); ?>
<?php ProjectTaskComp::renderEditTaskModal($data['members'], $data['project']); ?>

<script src="/assets/scripts/taskModal.js" nonce="<?= $data['csp_nonce'] ?? '' ?>"></script>
</body>