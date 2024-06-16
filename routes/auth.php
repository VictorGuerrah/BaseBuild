<?php
use App\Core\Routing\Router;
use App\Controller\AuthControllerImplemented;

Router::prefix('auth/')->group(function() {
    Router::post('view-login', [AuthControllerImplemented::class, 'view']);
    Router::post('check-authentication', [AuthControllerImplemented::class, 'checkAuthentication']);
    Router::post('validate-credentials', [AuthControllerImplemented::class, 'validateCredentials']);
});
