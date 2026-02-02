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
</body>

<script nonce="<?= $data['csp_nonce'] ?? '' ?>">
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