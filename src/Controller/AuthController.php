<?php

namespace App\Controller;

use App\Core\Classes\Request;
use App\Core\Classes\Response;
use App\Model\Entity\UserModel;
use App\Repository\UserRepositoryImplemented;
use App\Model\ValuableObject\Email;
use App\Model\ValuableObject\Password;
use App\Service\AuthService;

class AuthController extends BaseControllerImplemented
{
    protected Response $response;
    protected UserModel $userModel;
    protected UserRepositoryImplemented $userRepository;

    public function __construct(Response $response, UserRepositoryImplemented $userRepository)
    {
        $this->response = $response;
        $this->userRepository = $userRepository;
    }

    public function index(): void
    {
        $this->response->sendView('auth/login');
    }

    public function checkAuthentication(): void
    {

        $this->response->sendJson(['isAuthenticated' => false]);
    }

    public function validateCredentials(Request $request)
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
