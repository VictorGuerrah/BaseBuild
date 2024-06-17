<?php

require_once dirname(__FILE__, 2) . '/config/configuration.php';

use App\Interfaces\Service\ViewServiceInterface;
use App\Service\ViewServiceImplemented;

ViewServiceImplemented::load('index');
