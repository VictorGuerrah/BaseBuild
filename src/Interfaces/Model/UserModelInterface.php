<?php

namespace App\Interfaces\Model;

interface UserModelInterface
{
    public function getID(): string;
    public function getEmail(): string;
    public function getPasswordHash(): string;
}
