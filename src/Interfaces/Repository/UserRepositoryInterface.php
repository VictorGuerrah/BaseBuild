<?php

namespace App\Interfaces\Repository;

use App\Interfaces\Model\UserModelInterface;

interface UserRepositoryInterface extends BaseRepositoryInterface
{
    public function insert(UserModelInterface $user): void;
}
