/**
 * Task Modal Handler
 * Handles opening and closing of the add task modal
 */
(() => {
    // Modal elements
    const addTaskModal = document.getElementById('addTaskModal');
    const closeModalBtn = document.getElementById('closeModalBtn');
    const taskStatusSelect = document.getElementById('taskStatus');
    const addTaskForm = document.getElementById('addTaskForm');

    // Helper function to close the modal
    const closeModal = () => {
        if (addTaskModal) {
            addTaskModal.classList.add('hidden');
            addTaskModal.classList.remove('flex');
            // Reset form
            if (addTaskForm) {
                addTaskForm.reset();
            }
        }
    };

    // Helper function to escape HTML to prevent XSS
    const escapeHtml = (text) => {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    };

    // Helper function to create and add task card to the appropriate column
    const addTaskToColumn = (task) => {
        // Find the target column by status
        const columnId = `taskColumn-${task.status}`;
        const column = document.getElementById(columnId);

        if (!column) {
            console.error('Could not find column:', columnId);
            return;
        }

        // Create the task card HTML
        const taskCard = document.createElement('div');
        taskCard.className = 'tess-project-card w-full flex flex-col justify-between h-44';
        taskCard.setAttribute('data-task-id', task.id);

        // Escape user-generated content to prevent XSS
        const title = escapeHtml(task.title || '');
        const description = escapeHtml(task.description || '');

        // Get all task statuses for the move buttons
        const statuses = ['Backlog', 'ToDo', 'Doing', 'Review', 'Done'];

        // Generate move buttons HTML
        const moveButtonsHtml = statuses.map(status => `
            <button class='move-btn flex-1 border bg-neutral-600 border-neutral-700 text-white hover:brightness-50 cursor-pointer py-1 rounded'
                    data-task-id='${task.id}'
                    data-move-to='${status}'
                    title='Move to ${status}'>
                ${status.charAt(0)}
            </button>
        `).join('');

        taskCard.innerHTML = `
            <div>
                <span class='text-white block truncate'>${title}</span>
                <p class='text-xs font-medium line-clamp-5 wrap-break-word hyphens-auto'>
                    ${description}
                </p>
            </div>
            <div>
                <div class='w-full flex justify-between items-center'>
                    ${moveButtonsHtml}
                </div>
            </div>
        `;

        // Insert the task card at the bottom, right before the "Add Task" button
        const addTaskBtn = column.querySelector('.add-task-btn');
        if (addTaskBtn) {
            column.insertBefore(taskCard, addTaskBtn);
        } else {
            // If no add button found, just append to the column
            column.appendChild(taskCard);
        }
    };

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
        closeModalBtn.addEventListener('click', closeModal);
    }

    // Close modal when clicking outside of it
    if (addTaskModal) {
        addTaskModal.addEventListener('click', function (event) {
            if (event.target === addTaskModal) {
                closeModal();
            }
        });
    }

    // Handle form submission via AJAX
    if (addTaskForm) {
        addTaskForm.addEventListener('submit', async function (event) {
            event.preventDefault();

            // Get form data
            const formData = new FormData(this);

            // Get submit button to disable it during request
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn ? submitBtn.textContent : '';

            try {
                // Disable submit button
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.textContent = 'Creating...';
                }

                // Send AJAX request
                const response = await fetch('/api/task/create', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    // Success - close modal and dynamically add task to the DOM
                    closeModal();
                    addTaskToColumn(data.task);
                } else {
                    // Show error message
                    alert('Error: ' + (data.error || 'Failed to create task'));
                }

            } catch (error) {
                console.error('Error creating task:', error);
                alert('An unexpected error occurred. Please try again.');
            } finally {
                // Re-enable submit button
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalBtnText;
                }
            }
        });
    }
})();