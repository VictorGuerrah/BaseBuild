<?php

namespace App\Interfaces\Controller;

use App\Core\Classes\Request;

interface AuthControllerInterface
{
    public function view(): void;
    public function checkAuthentication(): void;
    public function validateCredentials(Request $request): void;
    public function logout(): void;

}
