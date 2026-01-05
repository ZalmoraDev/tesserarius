<?php

namespace App;

use ReflectionMethod;

class Router
{
    private array $controllers;

    public function __construct(array $controllers)
    {
        $this->controllers = $controllers;
    }

    // REFACTOR: This method is too long and does too many things, break it down into smaller methods

    public function route($uri): void
    {
        // Set default controller and method
        $DEFAULT_CONTROLLER = 'LoginController';
        $DEFAULT_METHOD = 'index';

        // Check if the request is an API request
        $isApi = false;
        if ($isApi = str_starts_with($uri, "api/")) {
            $uri = substr($uri, 4);
        }

        $uri = $this->stripParameters($uri);
        $explodedUri = explode('/', trim($uri, '/'));

        // Set controller and method
        $controllerName = !empty($explodedUri[0]) ? ucfirst(strtolower($explodedUri[0])) . "Controller" : $DEFAULT_CONTROLLER;
        $methodName = $explodedUri[1] ?? $DEFAULT_METHOD;
        $params = array_slice($explodedUri, 2);

        // Set proper controller paths
        $namespace = $isApi ? "app\\Controllers\\api\\" : "app\\Controllers\\";
        $controllerClass = $namespace . $controllerName;
        $controllerFile = dirname(__DIR__) . "/" . str_replace("\\", "/", $controllerClass) . ".php"; // TODO: Rework this filepath to be more dynamic

        // Check if controller exists
        if (!file_exists($controllerFile)) {
            http_response_code(404);
            var_dump($controllerFile);
            die("ROUTER: controller `{$controllerFile}` not found");
        }
        require_once $controllerFile;

        // Check if the controllers method exists
        if (!class_exists($controllerClass) || !method_exists($controllerClass, $methodName)) {
            http_response_code(404);
            die("ROUTER: Method `{$controllerFile} -> {$methodName}(...)`not found");
        }
        $controllerObj = new $controllerClass();

        try {
            $reflectionMethod = new ReflectionMethod($controllerObj, $methodName);
            $expectedParams = $reflectionMethod->getNumberOfParameters();

            if ($expectedParams > count($params)) {
                http_response_code(400);
                die("ROUTER: Missing parameters");
            }

            call_user_func_array([$controllerObj, $methodName], $params);
        } catch (\Throwable $e) {
            http_response_code(500);
            die("ROUTER: Internal Server Error: " . $e->getMessage());
        }
    }

    private function stripParameters($uri): string
    {
        return strtok($uri, '?');
    }
}