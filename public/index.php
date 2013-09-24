<?php
ini_set('display_errors',1);
error_reporting(E_ALL);
// Initialize Request Microtime
if (! defined('REQUEST_MICROTIME')) {
    define('REQUEST_MICROTIME', microtime(true));
}

// Initialize Base Dir
if (! defined('BASE_DIR')) {
    define('BASE_DIR', realpath(__DIR__ . "/../"));
}
// Initialize Request Microtime
if (! defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

// Define the application enviornment
if (! defined('APPLICATION_ENV')) {
    $envApplicationEnv = getenv("APPLICATION_ENV");
    define("APPLICATION_ENV", $envApplicationEnv ? $envApplicationEnv : "production");
}
/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */
chdir(dirname(__DIR__));

// Setup autoloading
require 'init_autoloader.php';

// Run the application!
Zend\Mvc\Application::init(require 'config/application.config.php')->run();
