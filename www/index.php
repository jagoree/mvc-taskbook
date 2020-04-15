<?php

use App\Core\Router;

require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'config/paths.php';
require_once APP . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

Router::launche();
