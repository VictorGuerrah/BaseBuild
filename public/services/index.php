<?php

use App\Core\Routing\Router;
use App\Core\Classes\Autowired;
use App\Core\Classes\HTTP;

include_once dirname(__FILE__, 3) . '/config/configuration.php';
include_once dirname(__FILE__, 3) . '/config/routes.php';

if (!isset($_GET['endpoint'])) {
    HTTP::sendResponse(404, "Missing endpoint.");
}

HTTP::setPost($_POST);

$endpoint = $_GET['endpoint'];
$route = Router::search($endpoint);

if ($route === null) {
    throw new Exception("Route not found");
}

$result = (new Autowired($route->class))->call($route->action);
echo $result;
