<?php
session_start();
error_reporting(0);
require(dirname(__FILE__) . '/core/protection.php');
require(dirname(__FILE__) . '/core/config.php');

Storage::memcache('init', ['status' => false]);
if(empty($_COOKIE['policy'])) Storage::cookie('set', ['title' => 'policy', 'val' => 'no']);
if(empty($_COOKIE['lang'])) Storage::cookie('set', ['title' => 'lang', 'val' => DEFAULT_LANG]);

$router = new Router($_SERVER['REQUEST_URI']);
define("CI", DS . ($router->getMethodPrefix() ? substr($router->getMethodPrefix(), 0, -1) . DS : '') . $router->getController());

$controller_class = ucfirst($router->getController()) . 'Controller';
$controller_method = strtolower($router->getMethodPrefix() . $router->getAction());

if(!class_exists($controller_class))
  Router::get_code(404, true);

$controller_object = new $controller_class;

if (method_exists($controller_object, $controller_method) == false)
	Router::get_code(404, true);

$results = Storage::get_default($controller_object->$controller_method($request, $storage));

$results['templates'] = ROOT . (($router->getRoute() == 'admin') ? ADMIN_TEMPLATE : TEMPLATE) . DS . 'templates';
$path = $results['templates'] . DS . $router->getController() . DS . $router->getMethodPrefix() . $router->getAction() . '.php';

if (file_exists($path) == false)
	throw new Exception("View '" . $results['templates'] . DS . $router->getController() . DS . $router->getMethodPrefix() . $router->getAction() . ".php' does not exists");

require_once($path);