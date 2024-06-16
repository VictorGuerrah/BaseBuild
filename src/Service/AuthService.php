<?php

namespace App\Service;

use App\Repository\UserRepositoryImplemented;

class AuthService
{
    private UserRepositoryImplemented $UserRepositoryImplemented;

    public function __construct(UserRepositoryImplemented $UserRepositoryImplemented)
    {
        $this->UserRepositoryImplemented = $UserRepositoryImplemented;
    }
}