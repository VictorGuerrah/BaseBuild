<?php

namespace App\Interfaces\Controller;

use App\Core\Http\Request;

interface AuthControllerInterface
{
    public function loginView(): void;
    public function dashboardView(): void;
    public function checkAuthentication(): void;
    public function validateCredentials(Request $request): void;
    public function logout(): void;

}
