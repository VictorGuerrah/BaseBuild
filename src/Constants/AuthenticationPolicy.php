<?php

namespace App\Constants;

class AuthenticationPolicy
{
    public const DEFAULT_KEEP_LOGGED_IN_TIMESTAMP = 24 * 60 * 3600;
    public const DEFAULT_ATTEMPTS = 5;
}
