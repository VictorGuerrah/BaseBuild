<?php

use App\Core\Routing\Router;
use App\Core\Classes\Autowired;
use App\Core\Classes\HTTP;

include_once dirname(__FILE__, 3) . '/config/configuration.php';
include_once dirname(__FILE__, 3) . '/config/routes.php';

try {
    if (!isset($_GET['endpoint'])) {
        throw new Exception("Missing endpoint.", 404);
    }

    HTTP::setPost($_POST);

    $endpoint = $_GET['endpoint'];
    $route = Router::search($endpoint);

    if ($route === null) {
        throw new Exception("Route not found for endpoint '$endpoint'.", 404);
    }

    $result = (new Autowired($route->class))->call($route->action);
    
    echo $result;
} catch (Exception $e) {
    HTTP::sendResponse($e->getCode(), $e->getMessage());
}
