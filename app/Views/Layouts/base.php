<?php

use App\Core\Escaper;

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">

    <!-- Tailwind CSS -->
    <link rel="stylesheet" href="/assets/styles/output.css">
    <title><?= Escaper::html($data['viewTitle'] ?? '') ?></title>
</head>

<?php
/** @var array $data /app/Core/View.php View::render */
require $data['viewFile'];
?>

</html>