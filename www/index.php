<?php
define('APP',  dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR);
define('TPL_PATH', APP . 'src' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR);

require_once APP . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

$app = new \App\Core\Application();