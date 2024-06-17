<?php

namespace App\Interfaces\Repository;

use App\Interfaces\Model\CurrentTokenModelInterface;

interface CurrentTokenRepositoryInterface
{
    public function insert(CurrentTokenModelInterface $currentToken): void;
}
