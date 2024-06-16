<?php

namespace App\Interfaces;

interface ContainerInterface
{
    public function get(string $class): mixed;
    public function has(string $class): bool;
}
