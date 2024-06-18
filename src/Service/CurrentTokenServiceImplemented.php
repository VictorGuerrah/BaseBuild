<?php

namespace App\Service;

use App\Core\Classes\Environment;
use App\Core\Classes\JWT;
use App\Interfaces\Repository\CurrentTokenRepositoryInterface;
use App\Interfaces\Service\CurrentTokenServiceInterface;
use App\Model\Entity\CurrentTokenModel;
use Exception;

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

        // Criar uma instância do modelo de token com os dados recebidos
        $newCurrentToken = new CurrentTokenModel($userId, $token, true);

        if (empty($currentToken)) {
            // Se não houver token existente para este usuário, inserir o novo token
            $this->currentTokenRepository->insert($newCurrentToken);
        } else {
            // Se já houver um token existente, atualizar com o novo token
            $this->currentTokenRepository->update($newCurrentToken);
        }
    }


    public function checkToken(string $userId, string $token): void
    {
        $currentToken = $this->currentTokenRepository->findOne($userId);

        if (empty($currentToken)) {
            throw new Exception("Token not found.");
        }

        if (!$currentToken->isValid() || !hash_equals($token, $currentToken->getHash())) {
            throw new Exception("Invalid Token.");
        }
    }
}
