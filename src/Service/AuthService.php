<?php

namespace App\Service;

use App\Model\ValuableObject\Email;
use App\Model\ValuableObject\Password;
use App\Repository\UserRepositoryImplemented;

class AuthService
{
    private UserRepositoryImplemented $userRepository;

    public function __construct(UserRepositoryImplemented $UserRepository)
    {
        $this->userRepository = $UserRepository;
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
}
