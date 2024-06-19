<?php

namespace App\Middleware;

use App\Interfaces\Repository\UserRepositoryInterface;
use App\Interfaces\Service\AuthServiceInterface;
use App\Interfaces\Service\CurrentTokenServiceInterface;

class AuthenticationMiddleware
{
    public function handle(CurrentTokenServiceInterface $currentTokenService, AuthServiceInterface $authService, UserRepositoryInterface $userRepository): void
    {
        try {
            $authData = $authService->getCookies();
            if (empty($authData)) {
                throw new \Exception("Invalid session.", 401);
            }

            $user = $userRepository->findById($authData['info']['userID']);
            $authService->setLoggedUser($user);
            $currentTokenService->checkToken($authData['info']['userID'], $authData['token']);
        } catch (\Exception $ex) {
            throw new \Exception($ex->getMessage());
        }
    }
}
