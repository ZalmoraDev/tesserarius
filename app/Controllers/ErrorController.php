<?php
namespace App\Controllers;

use App\Core\View;

final class ErrorController
{
    /** 404 Not Found */
    public function notFound(): void
    {
        http_response_code(404);
        View::render('errors/404.html', "404 Not Found");
    }

    /** 405 Method Not Allowed */
    public function methodNotAllowed(): void
    {
        http_response_code(405);
        View::render('errors/404.html', "405 Not Allowed");
    }
}