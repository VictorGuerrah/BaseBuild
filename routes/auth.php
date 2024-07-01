<?php
use App\Core\Routing\Router;
use App\Controller\AuthController;
use App\Middleware\AuthenticationMiddleware;

Router::prefix('auth/')->group(function() {
    Router::post('view-login', [AuthController::class, 'loginView']);
    Router::post('check-authentication', [AuthController::class, 'checkAuthentication']);
    Router::post('validate-credentials', [AuthController::class, 'validateCredentials']);
    Router::post('logout', [AuthController::class, 'logout']);
    Router::post('view-dashboard', [AuthController::class, 'dashboardView']);
});

// Router::prefix('auth/')->middleware(AuthenticationMiddleware::class)->group(function() {
// });