<?php
/**
 * PHP Expects:
 * - array $flash_errors (contains user-friendly error messages)
 */

// TODO: Change to class+method version like the other components?
?>

<?php if ($flash_errors): ?>
    <div id="toast-container" class="fixed top-6 right-6 z-[9999] flex flex-col gap-2">
        <?php foreach ($flash_errors as $msg): ?>
            <div class="cursor-pointer bg-red-600 hover:bg-red-700 text-white px-4 py-3 rounded-md shadow-lg
            transition-all duration-500 ease-in-out opacity-100 translate-y-0">
                <?= $msg ?>
            </div>
        <?php endforeach; ?>
    </div>

    <script>
        setTimeout(() => {
            const container = document.getElementById('toast-container');
            if (!container) return;

            container.querySelectorAll('div').forEach(toast => {
                toast.classList.remove('opacity-100', 'translate-y-0');
                toast.classList.add('opacity-0', '-translate-y-8');
            });

            setTimeout(() => container.remove(), 600);
        }, 5000);
    </script>
<?php endif; ?>
