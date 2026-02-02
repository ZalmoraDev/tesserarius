/**
 * Task Modal Handler
 * Handles opening and closing of the add task modal
 */
(() => {
    // Modal elements
    const addTaskModal = document.getElementById('addTaskModal');
    const closeModalBtn = document.getElementById('closeModalBtn');
    const taskStatusSelect = document.getElementById('taskStatus');

    // Open modal when clicking any "Add Task" button
    document.querySelectorAll('.add-task-btn').forEach(button => {
        button.addEventListener('click', function () {
            const status = this.getAttribute('data-status');
            // Set the dropdown to the clicked column's status
            if (taskStatusSelect && status) {
                taskStatusSelect.value = status;
            }
            // Show modal
            if (addTaskModal) {
                addTaskModal.classList.remove('hidden');
                addTaskModal.classList.add('flex');
            }
        });
    });

    // Close modal when clicking close button
    if (closeModalBtn) {
        closeModalBtn.addEventListener('click', function () {
            if (addTaskModal) {
                addTaskModal.classList.add('hidden');
                addTaskModal.classList.remove('flex');
            }
        });
    }

    // Close modal when clicking outside of it
    if (addTaskModal) {
        addTaskModal.addEventListener('click', function (event) {
            if (event.target === addTaskModal) {
                addTaskModal.classList.add('hidden');
                addTaskModal.classList.remove('flex');
            }
        });
    }
})();