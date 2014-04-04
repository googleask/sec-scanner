<?php
/**
 * Sec - Scanner 0.1
 * @Author Jarosław Kamiński
 * @Date 02-04-2014
 * Jah Bless
 */

include_once('includes/Controller.php');
//Init controller
$controller = new Controller();

// Get variables for controller
$method = isset($_GET['method']) ? htmlspecialchars($_GET['method'], ENT_QUOTES) : array();
$action = isset($_GET['action']) ? htmlspecialchars($_GET['action'], ENT_QUOTES) : array();
// Print all!
if (is_callable(array($controller, $method))) {
  print $controller->$method($action);
}
else {
  print $controller->index();
}
