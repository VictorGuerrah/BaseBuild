<?php

namespace App\Controller;

use App\Core\Classes\Request;
use App\Core\Classes\Response;
use App\Core\Classes\Validator;
use App\Interfaces\Controller\AuthControllerInterface;
use App\Interfaces\Service\AuthServiceInterface;

class AuthControllerImplemented implements AuthControllerInterface
{
    protected Response $response;
    protected Validator $validator;
    protected AuthServiceInterface $authService;

    public function __construct(Response $response, AuthServiceInterface $AuthService, Validator $validator)
    {
        $this->response = $response;
        $this->validator = $validator;
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

        if ($this->validator->sanitizeInput($request->get('email')) || $this->validator->sanitizeInput($request->get('password'))) {
            $this->response->sendJson(['isValidated' => false]);
        }

        try {
            $userId = $this->authService->validateCredentials($request->get('email'), $request->get('password'));
            if (!empty($userId)) {
                $this->authService->setCookies($userId);
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
