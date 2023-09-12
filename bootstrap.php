<?php
// start session and define key global constants
define('BASE_DIR', __DIR__);
define('HTML_DIR', BASE_DIR . '/templates/site');
define('SRC_DIR', BASE_DIR . '/src');
// set up error handling
error_reporting(E_ALL);
ini_set('display_errors', 0);   // change this to "1" during website development
ini_set('error_log', BASE_DIR . '/logs/error.log');
// uses Composer autoloader
require BASE_DIR . '/vendor/autoload.php';
// start session
session_start();
return require SRC_DIR . '/config/config.php';
