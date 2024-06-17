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

    public function save(string $userId, string $tokenHash): void
    {
        $currentToken = $this->currentTokenRepository->findOne($userId);
        $newCurrentToken = new CurrentTokenModel($userId, $tokenHash, true);

        if (empty($currentToken)) {
            $this->currentTokenRepository->insert($newCurrentToken);
        } else {
            $this->currentTokenRepository->update($newCurrentToken);
        }
    }
}
