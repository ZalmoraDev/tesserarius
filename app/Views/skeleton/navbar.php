<nav class="w-full bg-neutral-900 gap-2 p-2
        shadow-[0_5px_10px_rgba(0,0,0,1)] flex justify-between">
    <!-- Left -->
    <div class="flex flex-1 gap-4 justify-start items-center">
        <a href="<?= $_ENV['SITE_URL'] ?>/" class="transition-colors flex items-center hover:brightness-50">
            <img src="<?= $_ENV['SITE_URL'] ?>/assets/icons/logo/logoW.svg"
                 alt="" height="32" width="32"
                 class="w-8 h-8"/>
            <h1 class="text-xl">Tesserarius</h1>
        </a>
    </div>

    <!-- Center -->
    <div class="flex flex-1 gap-4 justify-center items-center">
        <!-- Leave this empty -->
    </div>

    <!-- Right -->
    <div class="flex flex-1 gap-4 justify-end items-center">
        <p><?= $_SESSION['username'] ?? "NO_USER"; ?></p>
        <form action="<?= $_ENV['SITE_URL'] ?>/auth/logout" method="post">
            <!-- TODO: Add CSRF token & fix padding/margin -->
        <button type="submit" class="transition-colors cursor-pointer">
            <img src="<?= $_ENV['SITE_URL'] ?>/assets/icons/logout-32dp.svg"
                 alt="logout"
                 height="32" width="32"
                 class="w-8 h-8 hover:brightness-50">
        </button>
        </form>
    </div>
</nav>