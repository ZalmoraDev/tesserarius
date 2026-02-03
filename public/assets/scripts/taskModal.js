/**
 * Task Modal Handler
 * Handles opening and closing of the add task modal and edit task modal
 */
(() => {
    // Modal elements
    const addTaskModal = document.getElementById('addTaskModal');
    const closeModalBtn = document.getElementById('closeModalBtn');
    const taskStatusSelect = document.getElementById('taskStatus');
    const addTaskForm = document.getElementById('addTaskForm');

    // Edit modal elements
    const editTaskModal = document.getElementById('editTaskModal');
    const closeEditModalBtn = document.getElementById('closeEditModalBtn');
    const editTaskForm = document.getElementById('editTaskForm');
    const deleteTaskBtn = document.getElementById('deleteTaskBtn');

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

    // Helper function to close the edit modal
    const closeEditModal = () => {
        if (editTaskModal) {
            editTaskModal.classList.add('hidden');
            editTaskModal.classList.remove('flex');
            // Reset form
            if (editTaskForm) {
                editTaskForm.reset();
            }
        }
    };

    // Helper function to open edit modal with task data
    const openEditModal = (taskData) => {
        if (!editTaskModal) return;

        // Populate form fields
        document.getElementById('edit_task_id').value = taskData.id;
        document.getElementById('edit_title').value = taskData.title;
        document.getElementById('edit_description').value = taskData.description;
        document.getElementById('edit_taskStatus').value = taskData.status;
        document.getElementById('edit_taskPriority').value = taskData.priority;
        document.getElementById('edit_taskAssignee').value = taskData.assignee || '';
        document.getElementById('edit_due_date').value = taskData.dueDate;
        document.getElementById('edit_created_at_display').textContent = taskData.createdAt || '-';

        // Show modal
        editTaskModal.classList.remove('hidden');
        editTaskModal.classList.add('flex');
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
        taskCard.className = 'tess-project-card w-full flex flex-col justify-between h-44 cursor-pointer hover:brightness-90 task-card';
        taskCard.setAttribute('data-task-id', task.id);

        // Escape user-generated content to prevent XSS
        const title = escapeHtml(task.title || '');
        const description = escapeHtml(task.description || '');

        // Set all data attributes
        taskCard.setAttribute('data-task-title', task.title);
        taskCard.setAttribute('data-task-description', task.description || '');
        taskCard.setAttribute('data-task-status', task.status);
        taskCard.setAttribute('data-task-priority', task.priority);
        taskCard.setAttribute('data-task-assignee', task.assigneeId || '');
        taskCard.setAttribute('data-task-due-date', task.dueDate);
        taskCard.setAttribute('data-task-created-at', task.creationDate);

        taskCard.innerHTML = `
            <div>
                <span class='text-white block truncate'>${title}</span>
                <p class='text-xs font-medium line-clamp-5 wrap-break-word hyphens-auto'>
                    ${description}
                </p>
            </div>
        `;

        // Add click event listener to open edit modal
        taskCard.addEventListener('click', function () {
            const taskData = {
                id: this.getAttribute('data-task-id'),
                title: this.getAttribute('data-task-title'),
                description: this.getAttribute('data-task-description'),
                status: this.getAttribute('data-task-status'),
                priority: this.getAttribute('data-task-priority'),
                assignee: this.getAttribute('data-task-assignee'),
                dueDate: this.getAttribute('data-task-due-date'),
                createdAt: this.getAttribute('data-task-created-at')
            };
            openEditModal(taskData);
        });

        // Insert the task card at the bottom, right before the "Add Task" button
        const addTaskBtn = column.querySelector('.add-task-btn');
        if (addTaskBtn) {
            column.insertBefore(taskCard, addTaskBtn);
        } else {
            // If no add button found, just append to the column
            column.appendChild(taskCard);
        }
    };

    // Helper function to update task in DOM
    const updateTaskInColumn = (task) => {
        // Find the existing task card
        const existingCard = document.querySelector(`.task-card[data-task-id="${task.id}"]`);

        if (!existingCard) {
            console.error('Could not find task card to update:', task.id);
            return;
        }

        const oldStatus = existingCard.getAttribute('data-task-status');
        const newStatus = task.status;

        // If status changed, move to different column
        if (oldStatus !== newStatus) {
            // Remove from old column
            existingCard.remove();
            // Add to new column
            addTaskToColumn(task);
        } else {
            // Update in place
            const title = escapeHtml(task.title || '');
            const description = escapeHtml(task.description || '');

            // Update data attributes
            existingCard.setAttribute('data-task-title', task.title);
            existingCard.setAttribute('data-task-description', task.description || '');
            existingCard.setAttribute('data-task-status', task.status);
            existingCard.setAttribute('data-task-priority', task.priority);
            existingCard.setAttribute('data-task-assignee', task.assigneeId || '');
            existingCard.setAttribute('data-task-due-date', task.dueDate);

            // Update displayed content
            existingCard.innerHTML = `
                <div>
                    <span class='text-white block truncate'>${title}</span>
                    <p class='text-xs font-medium line-clamp-5 wrap-break-word hyphens-auto'>
                        ${description}
                    </p>
                </div>
            `;

            // Re-attach click event listener
            existingCard.addEventListener('click', function () {
                const taskData = {
                    id: this.getAttribute('data-task-id'),
                    title: this.getAttribute('data-task-title'),
                    description: this.getAttribute('data-task-description'),
                    status: this.getAttribute('data-task-status'),
                    priority: this.getAttribute('data-task-priority'),
                    assignee: this.getAttribute('data-task-assignee'),
                    dueDate: this.getAttribute('data-task-due-date'),
                    createdAt: this.getAttribute('data-task-created-at')
                };
                openEditModal(taskData);
            });
        }
    };

    // Helper function to remove task from DOM
    const removeTaskFromColumn = (taskId) => {
        const taskCard = document.querySelector(`.task-card[data-task-id="${taskId}"]`);
        if (taskCard) {
            taskCard.remove();
        } else {
            console.error('Could not find task card to remove:', taskId);
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

    // Open edit modal when clicking any task card
    document.querySelectorAll('.task-card').forEach(card => {
        card.addEventListener('click', function () {
            const taskData = {
                id: this.getAttribute('data-task-id'),
                title: this.getAttribute('data-task-title'),
                description: this.getAttribute('data-task-description'),
                status: this.getAttribute('data-task-status'),
                priority: this.getAttribute('data-task-priority'),
                assignee: this.getAttribute('data-task-assignee'),
                dueDate: this.getAttribute('data-task-due-date'),
                createdAt: this.getAttribute('data-task-created-at')
            };
            openEditModal(taskData);
        });
    });

    // Close modal when clicking close button
    if (closeModalBtn) {
        closeModalBtn.addEventListener('click', closeModal);
    }

    // Close edit modal when clicking close button
    if (closeEditModalBtn) {
        closeEditModalBtn.addEventListener('click', closeEditModal);
    }

    // Close modal when clicking outside of it
    if (addTaskModal) {
        addTaskModal.addEventListener('click', function (event) {
            if (event.target === addTaskModal) {
                closeModal();
            }
        });
    }

    // Close edit modal when clicking outside of it
    if (editTaskModal) {
        editTaskModal.addEventListener('click', function (event) {
            if (event.target === editTaskModal) {
                closeEditModal();
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

    // Handle edit form submission via AJAX (UPDATE)
    if (editTaskForm) {
        editTaskForm.addEventListener('submit', async function (event) {
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
                    submitBtn.textContent = 'Updating...';
                }

                // Send AJAX request
                const response = await fetch('/api/task/edit', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    // Success - close modal and update task in DOM
                    closeEditModal();
                    updateTaskInColumn(data.task);
                } else {
                    // Show error message
                    alert('Error: ' + (data.error || 'Failed to update task'));
                }

            } catch (error) {
                console.error('Error updating task:', error);
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

    // Handle delete button click
    if (deleteTaskBtn) {
        deleteTaskBtn.addEventListener('click', async function () {
            const taskId = document.getElementById('edit_task_id').value;
            const originalBtnText = this.textContent;

            try {
                // Disable delete button
                this.disabled = true;
                this.textContent = 'Deleting...';

                // Get CSRF token from form
                const csrf = document.querySelector('#editTaskForm input[name="csrf"]').value;
                const projectId = document.querySelector('#editTaskForm input[name="project_id"]').value;

                // Create form data
                const formData = new FormData();
                formData.append('csrf', csrf);
                formData.append('task_id', taskId);
                formData.append('project_id', projectId);

                // Send AJAX request
                const response = await fetch('/api/task/delete', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    // Success - close modal and remove task from DOM
                    closeEditModal();
                    removeTaskFromColumn(taskId);
                } else {
                    // Show error message
                    alert('Error: ' + (data.error || 'Failed to delete task'));
                }

            } catch (error) {
                console.error('Error deleting task:', error);
                alert('An unexpected error occurred. Please try again.');
            } finally {
                // Re-enable delete button
                this.disabled = false;
                this.textContent = originalBtnText;
            }
        });
    }
})();