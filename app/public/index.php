<?php
header("Access-Control-Allow-Origin: *"); // Allows any website (*) to access this API (useful for public APIs).
header("Access-Control-Allow-Headers: *"); // Allows all HTTP request headers (useful for handling JSON requests, auth tokens, etc.).

require_once dirname(__DIR__) . '/vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(dirname('/'));
$dotenv->load();

$dotenv->required(['SITE_URL']);

$serverIp = $_SERVER['SERVER_ADDR'];
define('SITE_URL', $_ENV['SITE_URL']);

$uri = trim($_SERVER['REQUEST_URI'], '/');

$router = new App\Router();
$router->route($uri);
?>