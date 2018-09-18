<?php
/**
 * Created by PhpStorm.
 * User: Nzt200
 * Date: 18.09.2018
 * Time: 19:05
 */
define('ROOT',dirname(__FILE__,2));
require_once ROOT . '/components/Router.php';
$router = new Router();
$router->run();

