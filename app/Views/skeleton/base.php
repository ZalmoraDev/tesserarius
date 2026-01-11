<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">

    <!-- Tailwind CSS -->
    <script src="<?= $_ENV['SITE_URL'] ?>/assets/js/app.js"></script>
    <link rel="stylesheet" href="<?= $_ENV['SITE_URL'] ?>/assets/styles/output.css">
    <title><?= htmlspecialchars($titleRender ?? '') ?></title>
</head>

<?php
// TODO: Remove before production
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require $viewRender; // Ignore error, $viewRender is always set in controllers by View::render()
?>


</html>