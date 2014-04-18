<?php
/**
 * Sec - Scanner 0.1
 * @Author Jarosław Kamiński
 * @Date 02-04-2014
 * Jah Bless
 */

require_once(realpath(dirname(__FILE__)).'/includes/Router.php');
//Init controller
$router = new Router($_GET['method'],$_GET['action']);
$router->call();