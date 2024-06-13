<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Repository\UserRepositoryImplemented;
use App\Controller\LoginController;
use App\Database\Connection;

$config = require __DIR__ . '/../config/database.php';

$dbConnection = new Connection($config);
$pdo = $dbConnection->getPdo();

$userRepository = new UserRepositoryImplemented($pdo);
$loginController = new LoginController($userRepository);

$credentials = [
    'email' => 'usuario@example.com',
    'password' => 'senha123',
];

try {
    if ($loginController->login($credentials)) {
        echo "Login bem-sucedido!";
    } else {
        echo "Falha no login!";
    }
} catch (\Exception $e) {
    echo "Erro ao fazer login: " . $e->getMessage();
}
