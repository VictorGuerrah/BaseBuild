<?php

namespace App\Service;

use App\Repository\CurrentTokenRepository;
use App\Interfaces\Service\CurrentTokenServiceInterface;
use App\Model\Entity\CurrentTokenModel;

class CurrentTokenService implements CurrentTokenServiceInterface
{
    private CurrentTokenRepository $currentTokenRepository;

    public function __construct(CurrentTokenRepository $currentTokenRepository)
    {
        $this->currentTokenRepository = $currentTokenRepository;
    }

    public function save(string $userId, string $token): void
    {
        $newCurrentToken = new CurrentTokenModel($userId, $token, true);
        $this->currentTokenRepository->save($newCurrentToken);
    }

    public function checkToken(string $userId, string $token): void
    {
        $currentToken = $this->currentTokenRepository->findOneBy(['UserID' => $userId]);

        if (empty($currentToken)) {
            throw new \Exception("Token not found.", 401);
        }

        if (!$currentToken->isValid() || !hash_equals($token, $currentToken->getHash())) {
            throw new \Exception("Invalid Token.", 401);
        }
    }
}
