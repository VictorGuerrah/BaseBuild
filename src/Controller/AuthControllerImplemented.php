<?php

namespace App\Controller;

use App\Core\Classes\Request;
use App\Core\Classes\Response;
use App\Core\Classes\Validator;
use App\Interfaces\Controller\AuthControllerInterface;
use App\Interfaces\Service\AuthServiceInterface;
use App\Interfaces\Service\CurrentTokenServiceInterface;
use Exception;

class AuthControllerImplemented implements AuthControllerInterface
{
    protected Response $response;
    protected Validator $validator;
    protected AuthServiceInterface $authService;
    protected CurrentTokenServiceInterface $currentTokenService;

    public function __construct(Response $response, AuthServiceInterface $authService, Validator $validator, CurrentTokenServiceInterface $currentTokenService)
    {
        $this->response = $response;
        $this->validator = $validator;
        $this->authService = $authService;
        $this->currentTokenService = $currentTokenService;
    }

    public function loginView(): void
    {
        $this->response->sendView('auth/login');
    }

    public function dashboardView(): void
    {
        $this->response->sendView('auth/dashboard');
    }

    public function checkAuthentication(): void
    {
        try {
            $authData = $this->authService->getCookies();
            
            if (empty($authData)) {
                $this->response->sendJson(['isAuthenticated' => false]);
            }
        
            $this->currentTokenService->checkToken($authData['info']['userID'], $authData['token']);
            $this->response->sendJson(['isAuthenticated' => true]);
        
        } catch (Exception $ex) {
            $this->response->sendJson(['isAuthenticated' => false]);
        }
        
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
        $this->authService->logout();
        $this->response->sendJson();
    }
}
