<?php

namespace App\Repository;

use App\Model\Entity\UserModel;
use App\Model\ValuableObject\Email;

class UserRepository extends BaseRepository
{
    protected string $table = 'users';

    protected function mapResults(array $results): array
    {
        return array_map(function ($result) {
            return new UserModel(new Email($result['Email']), $result['Password'], $result['ID']);
        }, $results);
    }
}
