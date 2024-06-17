<?php

namespace App\Service;

use App\Constants\AuthenticationPolicy;
use App\Core\Classes\JWT;
use App\Interfaces\Repository\UserRepositoryInterface;
use App\Model\ValuableObject\Email;
use App\Model\ValuableObject\Password;

class AuthService
{
    private UserRepositoryInterface $userRepository;
    private CurrentTokenService $currentTokenService;

    public function __construct(UserRepositoryInterface $UserRepository, CurrentTokenService $currentTokenService)
    {
        $this->userRepository = $UserRepository;
        $this->currentTokenService = $currentTokenService;
    }

    public function validateCredentials(string $email, string $password): bool
    {
        $email = new Email($email);

        $user = $this->userRepository->getByEmail($email);

        if (!$user) {
            return false;
        }

        $passwordObject = new Password($password);
        if (!$passwordObject->verify($user->getPasswordHash())) {
            return false;
        }

        return true;
    }

    public function setCookies(string $userID): void
    {
        $keepLoggedInUntil = time() + AuthenticationPolicy::DEFAULT_KEEP_LOGGED_IN_TIMESTAMP;

        $info = json_encode([
            'userID' => $userID,
            'time' => time()
        ]);

        $token = JWT::create('', $keepLoggedInUntil);
        $tokenHash = password_hash($token, PASSWORD_BCRYPT);

        $this->currentTokenService->save($userID, $tokenHash);
    }
}
