<?php

use App\Core\Routing\Router;
use App\Core\Classes\HTTP;
use App\Core\Classes\Autowired;

include_once dirname(__FILE__, 3) . '/config/configuration.php';
include_once dirname(__FILE__, 3) . '/config/routes.php';

$container = include_once dirname(__FILE__, 3) . '/config/bootstrap.php';

try {
    $endpoint = $_GET['endpoint'] ?? null;
    if (!$endpoint) {
        throw new Exception("Missing endpoint.", 404);
    }

    HTTP::setPost($_POST);

    $route = Router::search($endpoint);
    if ($route === null) {
        throw new Exception("Route not found for endpoint '$endpoint'.", 404);
    }

    // $middlewares = $route->getMiddlewares();
    // if (!empty($middlewares)) {
    //     foreach ($middlewares as $middleware) {
    //         $middlewareInstance = $container->get($middleware);
    //         $autowired = new Autowired($middleware, $container);
    //         $autowired->call('handle', $middlewareInstance);
    //     }
    // }

    $controller = $container->get($route->class);
    $autowired = new Autowired($route->class, $container);
    $result = $autowired->call($route->action, $controller);

    echo $result;
} catch (Exception $e) {
    HTTP::sendResponse($e->getCode(), $e->getMessage());
}
