<?php
use App\Core\Routing\Router;
use App\Controller\AuthControllerImplemented;
use App\Middleware\AuthenticationMiddleware;

Router::prefix('auth/')->group(function() {
    Router::post('view-login', [AuthControllerImplemented::class, 'loginView']);
    Router::post('check-authentication', [AuthControllerImplemented::class, 'checkAuthentication']);
    Router::post('validate-credentials', [AuthControllerImplemented::class, 'validateCredentials']);
    Router::post('logout', [AuthControllerImplemented::class, 'logout']);
    Router::post('view-dashboard', [AuthControllerImplemented::class, 'dashboardView']);
});

// Router::prefix('auth/')->middleware(AuthenticationMiddleware::class)->group(function() {
// });