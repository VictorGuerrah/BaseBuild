<?php
use App\Core\Routing\Router;
use App\Controller\AuthController;

Router::prefix('auth/')->group(function() {
    Router::post('view-login', [AuthController::class, 'index']);
    Router::post('check-authentication', [AuthController::class, 'checkAuthentication']);
    Router::post('validate-credentials', [AuthController::class, 'validateCredentials']);
});
