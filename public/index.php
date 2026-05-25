<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('PUBLIC_PATH', __DIR__);
define('CONFIG_PATH', ROOT_PATH . '/config');

date_default_timezone_set('Asia/Jakarta');

require_once ROOT_PATH . '/vendor/autoload.php';
require_once APP_PATH . '/Core/Helpers.php';

use App\Core\App;

$app = new App();
$app->run();
