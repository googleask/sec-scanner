<?php
include_once('config/config.php');
include_once('includes/Scanner.php');

if (!empty($argv[1])) {
  $project_id = intval($argv[1]);
  $scanner = new Scanner($project_id);
  $scanner->performScan();
}