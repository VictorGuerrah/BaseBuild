<?php
use App\Core\Routing\Router;
use App\Controller\AuthController;

Router::post('auth/checkAuthentication', [AuthController::class, 'checkAuthentication']);
Router::post('auth/login', [AuthController::class, 'index']);
