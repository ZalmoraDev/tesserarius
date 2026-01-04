<?php
$dotenv = parse_ini_file('../.env');

$dbType = "mysql";
$servername = $dotenv['DB_HOST'];
$database = $dotenv['DB_DATABASE'];
$username = $dotenv['DB_USERNAME'];
$password = $dotenv['DB_PASSWORD'];

?>