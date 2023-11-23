<?php
ini_set('display_errors', false);
date_default_timezone_set( 'Asia/Novokuznetsk');

define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(dirname(__FILE__)));

define('DEFAULT_CONTROLLER', 'homepage');
define('DEFAULT_ACTION', 'index');
define('DEFAULT_LANG', 'ru');

define('ITEMS_PER_PAGE_LIST', [10, 25, 50, 100]);
define('ADMIN_ITEMS_PER_PAGE', 25);
define('DEFAULT_ITEMS_PER_PAGE', 12);
define('DEFAULT_SORTING', 'id asc');
define('DEFAULT_SORTING_TYPE', 'asc');
define('DEFAULT_DISPLAY_STATUS', 1);

$protocol = ($_SERVER['HTTP_X_FORWARDED_PORT'] == 443) ? 'https://' : 'http://';
define('SITE_URL', $protocol . $_SERVER['SERVER_NAME']);

/*** PATH ***/
define('VIEWS', 'views');
define('TEMPLATE', DS . VIEWS . DS . 'default');
define('ADMIN_TEMPLATE', DS . VIEWS . DS . 'admin');
define('CONF', TEMPLATE . DS . 'data');

define('MODELS', __DIR__ . DS . 'models');
define('CONTROLLERS', __DIR__ . DS . 'controllers');
define('EXTENSIONS', __DIR__ . DS . 'extensions');

define('IMG', 'img');

/*** AUTOLOADER ***/
spl_autoload_register(function ($class_name) {
  if (preg_match('/Controller$/', $class_name)):
    require_once(CONTROLLERS . DS . preg_replace('/controller$/', '.', strtolower($class_name)) . 'controller.php');
  else:
    require_once(MODELS . DS . ucfirst($class_name) . '.php');
  endif;
});

/*** ERROR ***/
function handleException( $exception ) {
  error_log( $exception->getMessage() );
  echo '<p><b>' . Lang::get('service_title', 'Message') . ':</b> <code>' . $exception->getMessage() . '</code></p>';
  echo '<p><b>' . Lang::get('service_title', 'File') . ':</b> <code>' . $exception->getFile() . '</code></p>';
  echo '<p><b>' . Lang::get('service_title', 'Line') . ':</b> <code>' . $exception->getLine() . '</code></p>';
}
set_exception_handler( 'handleException' );