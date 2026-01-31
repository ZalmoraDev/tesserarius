<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">

    <!-- Tailwind CSS -->
    <link rel="stylesheet" href="/assets/styles/output.css">
    <title><?= escape($data['viewTitle'] ?? '') ?></title>
</head>

<?php
// TODO: Remove before production
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/** Escape HTML special characters in a string to prevent XSS attacks */
function escape(string $string): string {
    return htmlspecialchars($string, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

/** @var array $data /app/Core/View.php View::render */
require $data['viewFile'];
?>

</html>