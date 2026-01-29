<?php

use App\Views\components\projectTaskComp;

// TODO: REMOVE THIS GLOBAL
global $allColumnTasksArray;

// TODO: Use ViewModel

// REFACTOR: Move logic to controller params?
$tasks1Backlog = $allColumnTasksArray[0] ?? [];
$tasks2ToDo = $allColumnTasksArray[1] ?? [];
$tasks3Doing = $allColumnTasksArray[2] ?? [];
$tasks4Review = $allColumnTasksArray[3] ?? [];
$tasks5Done = $allColumnTasksArray[4] ?? [];

$projectTask = new projectTaskComp();
?>

<body class="tess-base-body flex flex-col">
<?php include_once __DIR__ . "/../skeleton/navbar.php"; ?>
<main class="flex-1 flex justify-center items-start px-4 py-6">
    <div class="flex gap-4 items-start">

        <!--TODO: Create columns components through components/compProjectTask -->
        <div class="tess-base-container-sm rounded-xl flex flex-col w-56 min-w-48 gap-2">
            <div class="flex flex-col gap-2 w-full items-center">
                <h1 class="text-lg font-bold text-amber-400">1 <span class="text-white">Backlog</span></h1>
                <hr class='w-full px-4 border-neutral-600'>
            </div>
            <div id="taskColumn1" class="flex flex-col gap-2">
                <?php
                foreach ($tasks1Backlog ?? [] as $task) {
                    echo $projectTask->printProjectTask($task);
                }
                ?>
            </div>
            <div>
                <?= $projectTask->printAddProjectTask(); ?>
            </div>
        </div>

        <div class="tess-base-container-sm rounded-xl flex flex-col w-56 min-w-48 gap-2">
            <div class="flex flex-col gap-2 w-full items-center">
                <h1 class="text-lg font-bold text-amber-400">2 <span class="text-white">To-Do</span></h1>
                <hr class='w-full px-4 border-neutral-600'>
            </div>
            <div id="taskColumn2" class="flex flex-col gap-2">
                <?php
                foreach ($tasks2ToDo ?? [] as $task) {
                    echo $projectTask->printProjectTask($task);
                }
                ?>
            </div>
            <div>
                <?= $projectTask->printAddProjectTask(); ?>
            </div>
        </div>

        <div class="tess-base-container-sm rounded-xl flex flex-col w-56 min-w-48 gap-2">
            <div class="flex flex-col gap-2 w-full items-center">
                <h1 class="text-lg font-bold text-amber-400">3 <span class="text-white">Doing</span></h1>
                <hr class='w-full px-4 border-neutral-600'>
            </div>
            <div id="taskColumn3" class="flex flex-col gap-2">
                <?php
                foreach ($tasks3Doing ?? [] as $task) {
                    echo $projectTask->printProjectTask($task);
                }
                ?>
            </div>
            <div>
                <?= $projectTask->printAddProjectTask(); ?>
            </div>
        </div>

        <div class="tess-base-container-sm rounded-xl flex flex-col w-56 min-w-48 gap-2">
            <div class="flex flex-col gap-2 w-full items-center">
                <h1 class="text-lg font-bold text-amber-400">4 <span class="text-white">Review</span></h1>
                <hr class='w-full px-4 border-neutral-600'>
            </div>
            <div id="taskColumn4" class="flex flex-col gap-2">
                <?php
                foreach ($tasks4Review ?? [] as $task) {
                    echo $projectTask->printProjectTask($task);
                }
                ?>
            </div>
            <div>
                <?= $projectTask->printAddProjectTask(); ?>
            </div>
        </div>

        <div class="tess-base-container-sm rounded-xl flex flex-col w-56 min-w-48 gap-2">
            <div class="flex flex-col gap-2 w-full items-center">
                <h1 class="text-lg font-bold text-amber-400">5 <span class="text-white">Done</span></h1>
                <hr class='w-full px-4 border-neutral-600'>
            </div>
            <div id="taskColumn5" class="flex flex-col gap-2">
                <?php
                foreach ($tasks5Done ?? [] as $task) {
                    echo $projectTask->printProjectTask($task);
                }
                ?>
            </div>
            <div>
                <?= $projectTask->printAddProjectTask(); ?>
            </div>
        </div>
    </div>
</main>
</body>

<script>
    // Move tickets between columns
    // TODO: NOT REAL TIME FOR OTHER USERS, ONLY ON PAGE RELOAD WILL TICKETS BE MOVED FOR OTHERS
    document.addEventListener("DOMContentLoaded", function () {
        console.log("DOM fully loaded and parsed");

        // Add event listener to all the move buttons
        document.querySelectorAll('.move-btn').forEach(button => {
            button.addEventListener('click', function (event) {
                const moveTo = event.target.getAttribute('data-move-to');
                const taskCard = event.target.closest('.tess-project-card');
                const taskId = taskCard.getAttribute('data-task-id'); // Assuming task ID is set in data-task-id

                if (taskId && moveTo) {

                    // Send AJAX request to update task column in the database
                    fetch('/api/task/moveTask', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `task_id=${taskId}&new_column=${moveTo}`
                    })
                        .then(response => response.json())
                        .then(data => {
                            console.log('API response:', data); // Log the API response
                            if (data.status === 'success') {
                                const targetColumn = document.getElementById(`taskColumn${moveTo}`);
                                if (targetColumn && taskCard) {
                                    targetColumn.appendChild(taskCard); // Move the task card to the new column
                                }
                            } else {
                                alert(data.message); // Show error if the move failed
                            }
                        })
                        .catch(error => {
                            console.error("Error moving task:", error);
                            alert("Failed to move task");
                        });
                }
            });
        });
    });
</script>