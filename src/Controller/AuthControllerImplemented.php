<?php

namespace App\Controller;

use App\Core\Classes\Request;
use App\Core\Classes\Response;
use App\Interfaces\Controller\AuthControllerInterface;
use App\Model\Entity\UserModel;
use App\Service\AuthService;

class AuthControllerImplemented implements AuthControllerInterface
{
    protected Response $response;
    protected UserModel $userModel;
    protected AuthService $authService;

    public function __construct(Response $response, AuthService $AuthService)
    {
        $this->response = $response;
        $this->authService = $AuthService;
    }

    public function view(): void
    {
        $this->response->sendView('auth/login');
    }

    public function checkAuthentication(): void
    {

        $this->response->sendJson(['isAuthenticated' => false]);
    }

    public function validateCredentials(Request $request): void
    {
        $request->validate([
            'email' => 'required|email|string', 
            'password' => 'required|string'
        ]);
        try {
            if ($this->authService->validateCredentials($request->get('email'), $request->get('password'))) {
                $this->response->sendJson(['isValidated' => true]);
            }

            $this->response->sendJson(['isValidated' => false]);

        } catch (\Throwable $th) {
            throw new \Exception("Credentials error: " . $th->getMessage());
        }
    }

    public function logout(): void
    {
        // Implemente aqui a lógica para realizar o logout
    }
}
