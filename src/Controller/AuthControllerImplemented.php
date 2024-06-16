<?php

namespace App\Controller;

use App\Core\Classes\Request;
use App\Core\Classes\Response;
use App\Interfaces\Controller\AuthControllerInterface;
use App\Interfaces\Repository\UserRepositoryInterface;
use App\Model\Entity\UserModel;
use App\Model\ValuableObject\Email;
use App\Model\ValuableObject\Password;
use App\Service\AuthService;

class AuthControllerImplemented implements AuthControllerInterface
{
    protected Response $response;
    protected UserModel $userModel;
    protected UserRepositoryInterface $userRepository;

    public function __construct(Response $response, UserRepositoryInterface $userRepository)
    {
        $this->response = $response;
        $this->userRepository = $userRepository;
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
        try {

            $email = new Email($request->get('email'));
            $password = new Password($request->get('password'));

            $user = $this->userRepository->getByEmail($email);

            if (!$user) {
                throw new \Exception("Invalid credentials.");
            }

            if (!$password->verify($user->getPasswordHash())) {
                throw new \Exception("Invalid credentials.");
            }


        } catch (\Throwable $th) {
            throw new \Exception("Erro ao fazer login: " . $th->getMessage());
        }
    }

    public function logout(): void
    {
        // Implemente aqui a lógica para realizar o logout
    }
}
