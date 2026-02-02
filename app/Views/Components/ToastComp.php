<?php

namespace App\Views\Components;

/** Toast notification component for rendering flash messages
 * Uses:
 * - $msgs['successes']
 * - $msgs['info']
 * - $msgs['errors']
 */
final class ToastComp
{
    /** Array holding toast types (errors/info/success) and their configurations (colors, icon & alt text) */
    private const array TOAST_TYPES = [
            'errors' => [
                    'bgColor' => 'bg-red-600',
                    'hoverColor' => 'hover:bg-red-700',
                    'icon' => '/assets/icons/error_FFF.svg',
                    'alt' => 'Error'
            ],
            'info' => [
                    'bgColor' => 'bg-blue-600',
                    'hoverColor' => 'hover:bg-blue-700',
                    'icon' => '/assets/icons/info_FFF.svg',
                    'alt' => 'Info'
            ],
            'success' => [
                    'bgColor' => 'bg-green-600',
                    'hoverColor' => 'hover:bg-green-700',
                    'icon' => '/assets/icons/success_FFF.svg',
                    'alt' => 'Success'
            ]
    ];

    /** Render toast notifications by fetching flash data from session */
    public static function render(array $msgs, $cspNonce): void
    {
        // boolean tracking if a toast should be shown
        $hasToasts = false;
        foreach (array_keys(self::TOAST_TYPES) as $type) {
            if (!empty($msgs[$type])) {
                $hasToasts = true;
                break;
            }
        }

        if (!$hasToasts) return;

        self::renderToasts($msgs, $cspNonce);
    }

    /** Render HTML & JS for toast notifications */
    private static function renderToasts(array $msgs, string $cspNonce): void
    {
        ?>

        <div id="toast-container" class="fixed top-6 right-6 z-[9999] flex flex-col gap-2">
            <?php foreach (self::TOAST_TYPES as $type => $config): ?>
                <?php if (!empty($msgs[$type])): ?>
                    <?php foreach ($msgs[$type] as $msg): ?>
                        <div class="toast-item cursor-pointer <?= $config['bgColor'] ?> <?= $config['hoverColor'] ?> text-white px-4 py-3 rounded-md shadow-lg
                        transition-all duration-500 ease-in-out opacity-100 translate-y-0 flex items-center gap-3">
                            <img src="<?= $config['icon'] ?>" alt="<?= $config['alt'] ?>" class="w-6 h-6 shrink-0">
                            <span><?= $msg ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>

        <script nonce="<?= $cspNonce ?>">
            // Lambda on load to setup notification dismissal
            (() => {
                const container = document.getElementById('toast-container');
                if (!container) return;

                const toasts = container.querySelectorAll('.toast-item');

                // 1) Auto-dismiss after 3 seconds
                setTimeout(() => {
                    toasts.forEach(toast => {
                        if (toast.parentElement) {  // Check if still in DOM
                            toast.classList.remove('opacity-100', 'translate-y-0');
                            toast.classList.add('opacity-0', '-translate-y-8');
                        }
                    });
                }, 3000);

                // 2) Remove notification container after all toasts have faded out
                setTimeout(() => {
                    if (container.parentElement) {
                        container.remove();
                    }
                }, 4000);

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
        <?php
    }
}