<?php

require_once dirname(__FILE__, 2) . '/config/configuration.php';
// require_once dirname(__FILE__, 2) . '/src/Core/Helper/Util.php';

use App\Service\ViewService;

ViewService::load('index');
