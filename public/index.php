<?php

declare(strict_types=1);

use Tempest\Router\HttpApplication;

ini_set('memory_limit', '1024M');

require_once __DIR__ . '/../vendor/autoload.php';

HttpApplication::boot(__DIR__ . '/../')->run();

exit();
