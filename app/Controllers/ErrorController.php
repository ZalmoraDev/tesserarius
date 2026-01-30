<?php
namespace App\Controllers;

use App\Core\View;

/** Controller to handle GET error pages
 * - GET: display 404/405 pages */
final readonly class ErrorController
{
    /** 404 Not Found */
    public function notFound(): void
    {
        http_response_code(404);
        View::render('Error/404.html', "404 Not Found");
    }

    /** 405 Method Not Allowed */
    public function methodNotAllowed(): void
    {
        http_response_code(405);
        View::render('Error/404.html', "405 Not Allowed");
    }
}