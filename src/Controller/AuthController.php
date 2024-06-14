<?php

namespace App\Controller;

use App\Core\Classes\Response;
use App\Core\Classes\Request;
use App\Model\Entity\UserModel;
use App\Repository\UserRepositoryImplemented;
use App\Model\ValuableObject\Email;
use App\Model\ValuableObject\Password;

class AuthController extends BaseControllerImplemented
{
    protected UserModel $userModel;
    protected UserRepositoryImplemented $userRepository;

    public function __construct(Response $response, UserRepositoryImplemented $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function index(): void
    {
        response()->view('auth/login');
    }

    public function checkAuthentication(): string
    {
        return $this->responseJson(['isAuthenticated' => false]);
    }

    public function login(array $credentials)
    {
        try {
            $email = new Email($credentials['email']);
            $password = new Password($credentials['password']);

            $user = $this->userRepository->getByEmail($email);

            if (!$user) {
                throw new \Exception("Invalid credentials.");
            }

            if (!$password->verify($user->getPasswordHash())) {
                throw new \Exception("Invalid credentials.");
            }

            return true;

        } catch (\Throwable $th) {
            throw new \Exception("Erro ao fazer login: " . $th->getMessage());
        }
    }

    public function logout()
    {
        // Implemente aqui a lógica para realizar o logout
    }
}
