<?php

namespace App\Service;

use App\Interfaces\Repository\CurrentTokenRepositoryInterface;
use App\Interfaces\Service\CurrentTokenServiceInterface;
use App\Model\Entity\CurrentTokenModel;

class CurrentTokenServiceImplemented implements CurrentTokenServiceInterface
{
    private CurrentTokenRepositoryInterface $currentTokenRepository;

    public function __construct(CurrentTokenRepositoryInterface $currentTokenRepository)
    {
        $this->currentTokenRepository = $currentTokenRepository;
    }

    public function save(string $userId, string $token): void
    {
        $currentToken = $this->currentTokenRepository->findOne($userId);
        $newCurrentToken = new CurrentTokenModel($userId, $token, true);

        if (empty($currentToken)) {
            $this->currentTokenRepository->insert($newCurrentToken);
        } else {
            $this->currentTokenRepository->update($newCurrentToken);
        }
    }


    public function checkToken(string $userId, string $token): void
    {
        $currentToken = $this->currentTokenRepository->findOne($userId);

        if (empty($currentToken)) {
            throw new \Exception("Token not found.", 401);
        }

        if (!$currentToken->isValid() || !hash_equals($token, $currentToken->getHash())) {
            throw new \Exception("Invalid Token.", 401);
        }
    }
}
