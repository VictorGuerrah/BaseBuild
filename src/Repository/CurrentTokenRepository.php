<?php

namespace App\Repository;

use App\Interfaces\Model\CurrentTokenModelInterface;
use App\Model\Entity\CurrentTokenModel;

class CurrentTokenRepository extends BaseRepository
{
    protected string $table = 'current_token';

    public function mapResults(array $results): array
    {
        return array_map(function ($result) {
            return new CurrentTokenModel($result['UserID'], $result['TokenHash'], $result['IsValid']);
        }, $results);
    }
}
