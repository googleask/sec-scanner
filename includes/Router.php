<?php
require_once(realpath(dirname(__FILE__)).'/Controller.php');
/**
 * Class Router
 * Simple router class
 */
class Router{

  private $method;
  private $action;
  private $controller;

  public function __construct($method, $action){
    // Get variables for controller
    $this->method = isset($method) ? htmlspecialchars($method, ENT_QUOTES) : array();
    $this->action = isset($action) ? htmlspecialchars($action, ENT_QUOTES) : array();
    $this->controller = new Controller();
  }


  public function call()
  {
    if (is_callable(array($this->controller, $this->method))) {
      $method = (string)$this->method;
      print $this->controller->$method($this->action);
    }
    else {
      print $this->controller->index();
    }
  }

}