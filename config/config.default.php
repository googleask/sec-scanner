<?php

//DATABASE
define('MYSQL_SERVER', 'localhost');
define('MYSQL_DATABASE', 'scanner');
define('MYSQL_PORT', '3306');
define('MYSQL_USER', 'root');
define('MYSQL_PASSWORD', '');

//RULES FILE
define('RULES','config/rules.xml');

//DESTINATION FOR UNPACK PROJECTS
define('DESTINATION','/home/bumfank/scanner-projects');

//LIST OF FILES THAT CAN BE SCANNED
define('FILE_TYPES',json_encode(array('php','module','install')));

//DEFINE PHP CLI PATH
define('PHP','/usr/bin/php');