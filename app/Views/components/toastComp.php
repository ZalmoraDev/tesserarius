<?php
/** @var array $data View */

if ($data['flash_errors']): ?>
    <div id="toast-container" class="fixed top-6 right-6 z-[9999] flex flex-col gap-2">
        <?php foreach ($data['flash_errors'] as $msg): ?>
            <div class="toast-item cursor-pointer bg-red-600 hover:bg-red-700 text-white px-4 py-3 rounded-md shadow-lg
            transition-all duration-500 ease-in-out opacity-100 translate-y-0 flex items-center gap-3">
                <img src="/assets/icons/error_FFF.svg" alt="Error" class="w-6 h-6 shrink-0">
                <span><?= $msg ?></span>
            </div>
        <?php endforeach; ?>
    </div>

    <script>
        // Lambda on load to setup notification dismissal
        (() => {
            const container = document.getElementById('toast-container');
            if (!container) return;

            const toasts = container.querySelectorAll('.toast-item');

            // 1) Auto-dismiss after 4 seconds
            setTimeout(() => {
                toasts.forEach(toast => {
                    if (toast.parentElement) {  // Check if still in DOM
                        toast.classList.remove('opacity-100', 'translate-y-0');
                        toast.classList.add('opacity-0', '-translate-y-8');
                    }
                });
            }, 4000);

            // 2) Remove notification container after all toasts have faded out
            setTimeout(() => {
                if (container.parentElement) {
                    container.remove();
                }
            }, 5000);

            // 3) Dismiss early by click
            toasts.forEach(toast => {
                toast.addEventListener('click', function () {
                    this.classList.remove('opacity-100', 'translate-y-0');
                    this.classList.add('opacity-0', '-translate-y-8');
                    setTimeout(() => {
                        this.remove();
                        // Remove container if no toasts left
                        if (container.querySelectorAll('.toast-item').length === 0) {
                            container.remove();
                        }
                    }, 600);
                });
            });
        })();
    </script>
<?php endif; ?>