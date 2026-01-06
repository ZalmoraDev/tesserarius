<?php
ob_start(); // Start output buffering

global $view, $title; // Used and reachable in all sub-controllers
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">

    <!-- Tailwind CSS -->
    <script src="<?=$_ENV['SITE_URL']?>/assets/js/app.js"></script>
    <link href="/dist/styles.css" rel="stylesheet">
    <link rel="stylesheet" href="<?=$_ENV['SITE_URL']?>/assets/styles/output.css">
    <title><?= $title ?></title>
</head>

<?php
// Error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (file_exists($view)) {
    include_once $view;
}

ob_end_flush(); // End output buffering
?>

</html>