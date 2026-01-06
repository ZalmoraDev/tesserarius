<?php
namespace App\Controllers;

class ErrorController
{
    /// 404 Not Found
    public function notFound(): void
    {
        // TODO: Fix this horrible global variable hell
        global $title, $view;

        http_response_code(404);
        $title = "Page Not Found";
        $view = __DIR__ . '/../Views/errors/404.html';
        require __DIR__ . '/../Views/skeleton/base.php';
    }

    /// 405 Method Not Allowed
    public function methodNotAllowed(): void
    {
        global $title, $view;

        http_response_code(405);
        $title = "Method Not Allowed";
        $view = __DIR__ . '/../Views/errors/405.html';
        require __DIR__ . '/../Views/skeleton/base.php';
    }
}