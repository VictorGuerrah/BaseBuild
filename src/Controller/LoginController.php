<?php

namespace App\Controller;

use App\Model\Entity\UserModel;
use App\Repository\UserRepositoryImplemented;
use App\Model\ValuableObject\Email;
use App\Model\ValuableObject\Password;

class LoginController
{
    protected UserModel $userModel;
    protected UserRepositoryImplemented $userRepository;

    public function __construct(UserRepositoryImplemented $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function index()
    {
        // Implemente aqui a lógica para exibir a página de login
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


            // Verificar senha
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
